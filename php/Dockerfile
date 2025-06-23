FROM php:8.2-fpm

# Установим системные утилиты и libzip
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && apt-get clean

# Установим Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
