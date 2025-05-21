FROM php:8.3-fpm

# Обновление и установка системных зависимостей
RUN apt-get clean && apt-get update && apt-get install -y \
    git \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip opcache dom mbstring tokenizer xml xmlwriter \
    && docker-php-ext-enable pdo_mysql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-enable mbstring

# Настройка PHP
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html