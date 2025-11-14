FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libmemcached-dev \
        libmcrypt-dev \
        redis-tools \
        libssl-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        iconv mbstring mysqli pdo_mysql zip gd \
        bcmath sockets exif intl opcache \
    && pecl install redis mcrypt memcached \
    && docker-php-ext-enable redis mcrypt memcached \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1


WORKDIR /app

EXPOSE 9000

CMD ["php-fpm"]
