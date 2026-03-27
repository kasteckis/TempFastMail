#syntax=docker/dockerfile:1

# Versions
FROM dunglas/frankenphp:1.11-php8.5 AS frankenphp_upstream

# Base FrankenPHP image
FROM frankenphp_upstream AS frankenphp_base
WORKDIR /app
VOLUME /app/var/

# persistent / runtime deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    file \
    cron \
    supervisor \
    procps \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    install-php-extensions \
        @composer \
        apcu \
        intl \
        pdo_mysql \
        opcache \
        zip;

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && apt-get install -y nodejs

COPY --link frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/frankenphp/Caddyfile

# ====================== MODIFICATION POUR EMAILS PERMANENTS ======================
# On copie le crontab mais on va le désactiver plus bas
COPY --link frankenphp/crontab /etc/frankenphp/crontab

# IMPORTANT : On commente l'installation du cron pour la suppression automatique
# RUN crontab -u root /etc/frankenphp/crontab

# Si tu veux garder le cron mais désactiver seulement la suppression, laisse la ligne ci-dessus commentée
# ================================================================================

ENTRYPOINT ["docker-entrypoint"]
HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]

# Dev image
FROM frankenphp_base AS frankenphp_dev
ENV APP_ENV=dev
ENV XDEBUG_MODE=off
ENV FRANKENPHP_WORKER_CONFIG=watch
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN set -eux; \
    install-php-extensions xdebug;
COPY --link frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/
COPY --link frankenphp/supervisord_dev.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord"]

# Prod image (celle qui sera utilisée)
FROM frankenphp_base AS frankenphp_prod
ENV APP_ENV=prod
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/

COPY --link composer.* symfony.* ./
RUN set -eux; \
    composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

COPY --link --exclude=frankenphp/ . ./
COPY --link frankenphp/supervisord_prod.conf /etc/supervisor/conf.d/supervisord.conf

RUN set -eux
RUN mkdir -p var/cache var/log var/share
RUN composer dump-autoload --classmap-authoritative --no-dev
RUN composer dump-env prod
RUN chmod +x bin/console
RUN sync

CMD ["/usr/bin/supervisord"]
