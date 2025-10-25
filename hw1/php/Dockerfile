FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql

# Установка расширений через PECL
RUN pecl install redis memcached \
    && docker-php-ext-enable redis memcached

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
