FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
            libfreetype6-dev \
            libjpeg62-turbo-dev \
            libpng-dev \
            libonig-dev \
            libzip-dev \
            libssl-dev \
            libssl-dev \
            libmemcached-dev \
            unzip \
            git \
            curl \
            && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
            && pecl install --onlyreqdeps --force redis \
            && pecl install memcached \
            && rm -rf /tmp/pear \
            && docker-php-ext-enable redis \
            && docker-php-ext-enable memcached \
            && docker-php-ext-configure gd --with-freetype --with-jpeg

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /data

CMD ["php-fpm"]