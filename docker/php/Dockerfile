FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libssl-dev \
    curl \
    git \
    libmemcached-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /data

COPY ../../composer.json ./

RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm"]