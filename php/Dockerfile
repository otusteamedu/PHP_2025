FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libmemcached-dev zlib1g-dev libsasl2-dev pkg-config libssl-dev && \
    docker-php-ext-install pdo pdo_mysql zip && \
    pecl install redis && \
    pecl install memcached && \
    docker-php-ext-enable redis memcached && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
