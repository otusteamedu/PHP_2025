FROM php:8.4-fpm-alpine

RUN apk add --no-cache curl \
    wget \
    libzip-dev \
    postgresql-dev \
    postgresql-libs \
    icu-dev \
    bash \
    git \
    autoconf \
    linux-headers \
    g++ \
    make \
    mc \
    zip \
    zlib-dev \
    && docker-php-ext-install bcmath sockets zip intl pdo pdo_pgsql \
    && pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/app

RUN addgroup -g 1000 www \
    && adduser -u 1000 -G www -s /bin/sh -D www \
    && chown -R www:www /var/app

RUN mkdir -p /home/www/.composer && chown -R www:www /home/www

USER www

EXPOSE 9000

CMD ["php-fpm", "-F"]
