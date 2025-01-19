FROM php:8.2-fpm-alpine AS api-php-fpm

ENV APP_ENV=dev
ENV APCU_VERSION=5.1.23
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
		linux-headers \
		acl \
		fcgi \
		file \
		gettext \
		git \
		gnu-libiconv \
		bash \
		bash-completion \
		libuser \
		tzdata \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		autoconf \
		libc-dev \
		make \
		gcc \
	;
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

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual mysql-clien; \
	docker-php-ext-install -j$(nproc) pdo_mysql; \
	docker-php-ext-enable pdo_mysql; \
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json .
COPY composer.lock .
COPY symfony.lock .

RUN composer install --prefer-install=dist --no-progress --no-interaction --dev
RUN bin/console doctrine:database:drop --force
RUN bin/console doctrine:database:create
RUN bin/console doctrine:schema:create
RUN bin/console doctrine:fixtures:load --no-interaction --append

CMD ["php-fpm"]