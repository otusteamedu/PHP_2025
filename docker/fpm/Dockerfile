FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libonig-dev \
        libzip-dev \
        libmemcached-dev \
        libmcrypt-dev \
        libc-dev \
        libssl-dev \
        libz-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-enable intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /var/www

VOLUME /var/www

CMD ["php-fpm"]