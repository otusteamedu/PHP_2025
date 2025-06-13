
FROM php:8.4-fpm-bullseye

RUN apt update && \
    apt install --no-install-recommends -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    libmemcached-dev \
    git \
    unzip

RUN docker-php-ext-install \
    mysqli \
    zip

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev && pecl install amqp redis && docker-php-ext-enable amqp redis

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./overrides.ini /usr/local/etc/php/conf.d/overrides.ini

RUN apt -y autoremove && \
    apt clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/temp/*
