FROM php:8.2-fpm

WORKDIR /var/www

# Установим системные зависимости и PHP расширения
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install mbstring zip

# Копирование и установка сomposer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts

CMD ["php-fpm"]
