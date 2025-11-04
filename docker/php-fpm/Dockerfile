FROM php:8.3-fpm

WORKDIR /var/www/app

RUN apt-get update  \
    && apt-get install -y zlib1g-dev libzip-dev unzip libssh-dev librabbitmq-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
