# Используем конкретную, стабильную версию Debian (Bullseye)
FROM php:8.2-fpm-bullseye

# Устанавливаем рабочую директорию
WORKDIR /data/webserver

# Устанавливаем системные зависимости, включая libcurl4-openssl-dev для ext-curl
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nano \
    curl \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libzip-dev \
    libicu-dev \
    libmemcached-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_pgsql \
        intl \
        zip \
        sockets \
        bcmath \
        curl \
    && pecl install redis memcached \
    && docker-php-ext-enable redis memcached

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем кастомные конфигурации PHP
COPY redis-session.ini /usr/local/etc/php/conf.d/redis-session.ini

# Установка и настройка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
