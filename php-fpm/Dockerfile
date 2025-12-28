FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    libmcrypt-dev \
    libpq-dev \
    && pecl install redis memcached \
    && docker-php-ext-install -j$(nproc) iconv mbstring pdo_mysql pdo_pgsql zip \
    && docker-php-ext-enable pdo_mysql redis memcached

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /data

VOLUME /data

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
