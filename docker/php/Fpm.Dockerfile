FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev

COPY config/php.ini /usr/local/etc/php/

WORKDIR /var/www/app