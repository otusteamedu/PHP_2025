
FROM php:8.1-fpm

# Установка пакетов
RUN apt-get update && apt-get install -y \
    curl wget git libfreetype6-dev libjpeg62-turbo-dev \
    libpng-dev libonig-dev libzip-dev libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Расширения PHP
RUN docker-php-ext-install -j$(nproc) \
    iconv mbstring pdo pdo_pgsql pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Создаем директорию для сокета
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php && \
    chmod 755 /var/run/php

# Копируем ОСНОВНОЙ конфиг php-fpm (заменяем дефолтный)
COPY php-fpm.conf /usr/local/etc/php-fpm.conf

# Копируем дополнительные настройки PHP
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /data/application.local

# Проверяем конфигурацию при старте
CMD ["php-fpm", "-F", "-R"]