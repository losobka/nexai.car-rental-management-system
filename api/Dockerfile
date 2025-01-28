FROM php:8.2-fpm-alpine AS php-fpm

ENV APCU_VERSION=5.1.23
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
        libzip \
        icu \
	;

RUN apk add --no-cache icu-dev;
RUN apk add --no-cache libzip-dev;

RUN docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
	;

RUN pecl install \
		apcu-${APCU_VERSION} \
		xdebug \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		xdebug \
		intl \
		zip \
	; \
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	apk del .build-deps

RUN apk add --no-cache --virtual mysql-clien; \
	docker-php-ext-install -j$(nproc) pdo_mysql; \
	docker-php-ext-enable pdo_mysql;

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
VOLUME [ "/app" ]


COPY --link composer.* .
COPY --link *.lock .

RUN composer install --no-progress --no-interaction --dev --no-scripts

COPY --link --chown=root:root . ./


EXPOSE 9000