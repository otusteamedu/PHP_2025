FROM php:8.3-fpm

WORKDIR /var/www/app

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install -j$(nproc) mbstring zip pdo pdo_pgsql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
