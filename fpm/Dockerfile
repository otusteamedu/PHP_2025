
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmemcached-dev \
    libmcrypt-dev \
    && pecl install mcrypt memcached redis \
    && docker-php-ext-enable mcrypt memcached redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
