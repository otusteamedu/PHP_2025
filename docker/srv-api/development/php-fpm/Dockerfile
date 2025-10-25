FROM php:8.3-fpm-alpine

RUN apk update && apk add fcgi linux-headers \
    && apk add --virtual build-deps autoconf postgresql-dev icu-dev libevent-dev openssl-dev zlib-dev libzip libzip-dev zlib \
  zlib-dev bzip2 libpng libpng-dev libjpeg libjpeg-turbo-dev libwebp-dev freetype freetype-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql pdo pdo_mysql -j$(getconf _NPROCESSORS_ONLN) intl gd bcmath sockets zip pcntl

RUN apk add --virtual .build-deps $PHPIZE_DEPS \
    && apk add --update linux-headers \
    && pecl install xdebug-3.4.0

RUN pecl update-channels pecl.php.net \
    install -o -f install redis \
    rm -rf /tmp/pear \
    docker-php-ext-enable redis.so \
RUN docker-php-ext-enable xdebug pgsql
RUN apk del -f .build-deps

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./php-fpm/conf.d /usr/local/etc/php/conf.d


WORKDIR /app
