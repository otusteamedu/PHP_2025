FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        libzip-dev \
        libmemcached-dev \
        zlib-dev \
        $PHPIZE_DEPS

RUN docker-php-ext-install pdo_mysql opcache

RUN pecl install redis memcached \
    && docker-php-ext-enable redis memcached

COPY php.ini /usr/local/etc/php/conf.d/custom.ini