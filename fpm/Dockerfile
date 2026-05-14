FROM php:8.2-fpm

# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
	libpng-dev \
	libonig-dev \
	libzip-dev \
	libmemcached-dev \
	libmcrypt-dev \
	libpq-dev \
        zlib1g-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo pdo_mysql zip pdo_pgsql pgsql \
        && pecl install -o -f redis memcached \
        &&  rm -rf /tmp/pear \
        &&  docker-php-ext-enable redis memcached

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
