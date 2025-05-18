FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        libmemcached-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd

RUN pecl install memcached redis && \
    docker-php-ext-enable memcached redis

COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /data

VOLUME /data