FROM php:8.2-fpm-alpine

# Установка зависимостей
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Установка PHP расширений
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    zip \
    gd \
    opcache

RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    make \
    linux-headers

RUN apk add --no-cache \
    postgresql-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql

RUN apk add --no-cache \
    && pecl install redis-5.3.7 \
    && docker-php-ext-enable redis


RUN apk add libmemcached-dev \
    zlib-dev \
    pkgconfig \
    && pecl install memcached \
    && docker-php-ext-enable memcached

RUN apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./composer.json
COPY composer.lock ./composer.lock

COPY /docker/php/entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]