FROM php:8.1-fpm

# Установка пакетов (без shadow)
RUN apt-get update && apt-get install -y \
    curl wget git libfreetype6-dev libjpeg62-turbo-dev \
    libpng-dev libonig-dev libzip-dev libpq-dev \
    procps \
    && rm -rf /var/lib/apt/lists/*

# Расширения PHP
RUN docker-php-ext-install -j$(nproc) \
    iconv mbstring pdo pdo_pgsql pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Настраиваем UID/GID пользователя www-data под твой ID (1000)
# Утилиты usermod и groupmod уже доступны в базовом образе
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Создаем директорию для сокета
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php && \
    chmod 755 /var/run/php

# Копируем конфиги
COPY php-fpm.conf /usr/local/etc/php-fpm.conf
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /data/application.local

# Важный момент по безопасности:
# Убираем флаг -R (он разрешает запуск от root). 
# Т.к. в docker-compose указано user: www-data, флаг -R не нужен и опасен.
CMD ["php-fpm", "-F"]