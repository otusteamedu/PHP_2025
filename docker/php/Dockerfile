FROM php:8.3-fpm-alpine
LABEL authors="Artem Plekhanov"

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"

RUN apk update && apk upgrade \
    && apk add --no-cache libldap openldap-dev \
    && apk add --no-cache zip libzip-dev libffi-dev libpng-dev postgresql-dev \
    && apk add --no-cache libjpeg-turbo-dev freetype freetype-dev libpq-dev \
    && apk add --no-cache libmcrypt-dev oniguruma-dev libxml2-dev openssl-dev \
    && apk add --no-cache cyrus-sasl-dev

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip pgsql pdo pdo_pgsql ldap sockets opcache \
    && docker-php-ext-enable redis.so \
    && docker-php-source delete
RUN apk clean cache \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY /conf/php.ini /usr/local/etc/php/php.ini
COPY /conf/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY /conf/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www

EXPOSE 9000