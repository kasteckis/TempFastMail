#syntax=docker/dockerfile:1

# Versions
FROM dunglas/frankenphp:1.11-php8.5 AS frankenphp_upstream

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# ---------------------------------------------------------------
# Builder: install prod vendor, dump autoload, warm Symfony cache
# ---------------------------------------------------------------
FROM frankenphp_upstream AS php_builder

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=prod

# hadolint ignore=DL3008
RUN apt-get update && apt-get install -y --no-install-recommends \
		git \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
		@composer \
		intl \
		pdo_mysql \
		zip \
	;

# Install dependencies first so the layer is cached when only sources change
COPY --link composer.* symfony.* ./
RUN --mount=type=cache,target=/tmp/composer-cache \
	COMPOSER_CACHE_DIR=/tmp/composer-cache \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

COPY --link --exclude=frankenphp/ . ./

RUN set -eux; \
	mkdir -p var/cache var/log var/share; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	chmod +x bin/console; \
	sync


# ---------------------------------------------------------------
# Builder: compile frontend assets via webpack-encore
# ---------------------------------------------------------------
FROM node:24-alpine AS node_builder

WORKDIR /app

# package.json references "file:vendor/symfony/ux-react/assets"
COPY --from=php_builder /app/vendor ./vendor

COPY --link package.json package-lock.json webpack.config.js tsconfig.json ./
COPY --link assets ./assets

RUN --mount=type=cache,target=/root/.npm \
	npm ci --no-audit --no-fund

RUN npm run build


# ---------------------------------------------------------------
# Base FrankenPHP image — minimal, shared by dev and prod
# ---------------------------------------------------------------
FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app

VOLUME /app/var/

# persistent / runtime deps
# hadolint ignore=DL3008
RUN apt-get update && apt-get install -y --no-install-recommends \
		cron \
		file \
		procps \
		supervisor \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

RUN set -eux; \
	install-php-extensions \
		apcu \
		intl \
		opcache \
		pdo_mysql \
		zip \
	;

ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

###> recipes ###
###< recipes ###

COPY --link frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/frankenphp/Caddyfile
COPY --link frankenphp/crontab /etc/frankenphp/crontab

RUN crontab -u root /etc/frankenphp/crontab

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]


# ---------------------------------------------------------------
# Dev image — composer + node + git + xdebug for live development
# ---------------------------------------------------------------
FROM frankenphp_base AS frankenphp_dev

ENV APP_ENV=dev \
	XDEBUG_MODE=off \
	FRANKENPHP_WORKER_CONFIG=watch \
	COMPOSER_ALLOW_SUPERUSER=1

# hadolint ignore=DL3008
RUN apt-get update \
	&& apt-get install -y --no-install-recommends git \
	&& curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
	&& apt-get install -y --no-install-recommends nodejs \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

RUN install-php-extensions @composer xdebug

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/
COPY --link frankenphp/supervisord_dev.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]


# ---------------------------------------------------------------
# Prod image — minimal runtime, pre-built vendor + assets
# ---------------------------------------------------------------
FROM frankenphp_base AS frankenphp_prod

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/

# Pre-built application: vendor, sources, warmed cache, bundle assets
COPY --from=php_builder --link /app /app
# Pre-built webpack assets
COPY --from=node_builder --link /app/public/build /app/public/build

COPY --link frankenphp/supervisord_prod.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
