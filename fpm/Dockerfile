FROM php:8.4-fpm

COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libicu-dev \
    default-mysql-client \
    libmemcached-dev \
    zlib1g-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl

RUN pecl install redis && docker-php-ext-enable redis

RUN pecl install memcached && docker-php-ext-enable memcached

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
