#!/bin/sh
set -e

if command -v composer >/dev/null 2>&1 && [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

if command -v npm >/dev/null 2>&1; then
  npm install
  npm run build
fi

# Display information about the current project
# Or about an error in project initialization
php bin/console -V

if grep -q ^DATABASE_URL= .env; then
  echo 'Waiting for database to be ready...'
  ATTEMPTS_LEFT_TO_REACH_DATABASE=60
  until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console dbal:run-sql -q "SELECT 1" 2>&1); do
    if [ $? -eq 255 ]; then
      echo "Doctrine command exits with 255, an unrecoverable error occurred."
      ATTEMPTS_LEFT_TO_REACH_DATABASE=0
      break
    fi
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
    echo "Still waiting for database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
  done

  if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo 'The database is not up or not reachable:'
    echo "$DATABASE_ERROR"
    exit 1
  else
    echo 'The database is now ready and reachable, running migrations ...'
    php bin/console doctrine:schema:update --force
    php bin/console app:make-user admin@admin.dev admin ROLE_ADMIN
    echo 'Migrations ran successfully!'
  fi
fi

php bin/console cache:clear --no-debug
php bin/console assets:install public --no-debug

echo 'PHP app ready!'

exec docker-php-entrypoint "$@"
