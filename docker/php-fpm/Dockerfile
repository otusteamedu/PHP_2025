FROM php:8.3-fpm-bullseye

# Устанавливаем необходимые зависимости для компиляции расширения
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip

# Устанавливаем Redis расширение для PHP
RUN pecl install redis && docker-php-ext-enable redis

# Копируем ваш файл php.ini
COPY ./php.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www