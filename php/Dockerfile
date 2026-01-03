FROM php:8.2-fpm

# 1. Установка системных зависимостей для компиляции расширений
RUN apt-get update && apt-get install -y \
    # Обязательно для любых PECL-расширений:
    gcc \
    g++ \
    make \
    autoconf \
    libc-dev \
    pkg-config \
    # Для memcached:
    libmemcached-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Установка встроенных расширений (не требуют компиляции)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3. Установка PECL-расширений
RUN pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install memcached \
    && docker-php-ext-enable memcached

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html