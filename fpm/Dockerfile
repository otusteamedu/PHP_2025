FROM php:8.4-fpm


RUN apt-get update && apt-get install -y \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libonig-dev libzip-dev libssl-dev libmemcached-dev zlib1g-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && pecl install redis memcached \
        && docker-php-ext-enable redis memcached \
        && apt-get clean

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /data

CMD ["php-fpm"]