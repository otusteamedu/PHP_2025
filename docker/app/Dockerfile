FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    apt-utils \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip unzip \
    git && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

USER root

RUN pecl channel-update pecl.php.net && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www