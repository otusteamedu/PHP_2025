FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libzstd-dev \
    libssl-dev \
    pkg-config \
    libpq-dev \
    build-essential \
    && pecl install redis \
    && pecl install memcached \
    && docker-php-ext-enable redis memcached \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get purge -y build-essential \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

