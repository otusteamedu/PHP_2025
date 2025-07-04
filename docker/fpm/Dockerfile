FROM php:8.2-fpm

WORKDIR /data

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    zip \
    git \
    curl \
    && docker-php-ext-install zip

# Копирование и установка сomposer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts

VOLUME /data

CMD ["php-fpm"]
