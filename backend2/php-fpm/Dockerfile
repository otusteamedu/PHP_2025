FROM php:8.3-fpm-alpine

# Зависимости
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    zlib-dev libzip-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql zip \
    && apk del .build-deps

WORKDIR /var/www/html