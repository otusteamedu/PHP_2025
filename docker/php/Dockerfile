FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    wget \
    icu-libs \
    libpng \
    libjpeg-turbo \
    libwebp \
    freetype \
    libzip \
    libevent \
    rabbitmq-c \
    postgresql-libs \
    openssl \
    ca-certificates \
    mc \
    git

RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    build-base \
    icu-dev \
    libevent-dev \
    openssl-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    rabbitmq-c-dev \
    freetype-dev \
    postgresql-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) intl gd bcmath pdo pdo_pgsql sockets zip \
    && pecl channel-update pecl.php.net \
    && pecl install -o -f redis amqp xdebug \
    && docker-php-ext-enable redis amqp xdebug \
    && rm -rf /tmp/pear \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/app

RUN addgroup -g 1000 www \
    && adduser -u 1000 -G www -s /bin/sh -D www \
    && chown -R www:www /var/app

USER www

EXPOSE 9000

CMD ["php-fpm", "-F"]
