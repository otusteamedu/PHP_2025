FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libmemcached-dev \
        libmcrypt-dev \
        libc-dev \
        libssl-dev \
        libz-dev \
        git \
        unzip \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
    && pecl install redis memcached \
    && docker-php-ext-enable redis memcached \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && curl -L https://phar.phpunit.de/phpunit.phar -o /usr/local/bin/phpunit \
    && chmod +x /usr/local/bin/phpunit

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /var/www

VOLUME /var/www

CMD ["php-fpm"]
