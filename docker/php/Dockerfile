FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    libzip \
    git \
    curl \
    wget \
    nano \
    rabbitmq-c \
    && apk add --no-cache --virtual .build-deps \
    oniguruma-dev \
    libzip-dev \
    autoconf \
    gcc \
    g++ \
    make \
    rabbitmq-c-dev \
    linux-headers \
    && docker-php-ext-install \
    opcache \
    zip \
    bcmath \
    sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apk del .build-deps

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer \
    && composer --version

WORKDIR /var/www/html

CMD ["php-fpm"]