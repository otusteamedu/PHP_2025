FROM php:8.4-fpm

# Установка необходимых модулей
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        bc \
        dnsutils \
        git \
        unzip \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd \
        && pecl install redis \
        && docker-php-ext-enable redis

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
WORKDIR /data
VOLUME /data

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Установка XDebug для покрытия кода
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

CMD ["php-fpm"]