FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/app