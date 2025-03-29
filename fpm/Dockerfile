FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        && pecl install redis \
        && docker-php-ext-enable redis \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

WORKDIR /data

CMD ["php-fpm"]