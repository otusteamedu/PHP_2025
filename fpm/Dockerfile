FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    curl \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmemcached-dev \
    libmcrypt-dev \
    librabbitmq-dev \
    && pecl install mcrypt memcached redis amqp \
    && docker-php-ext-enable mcrypt memcached redis amqp \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./zz-z.conf /usr/local/etc/php-fpm.d/zz-z.conf

RUN mkdir -p /var/log/app && chmod -R 777 /var/log/app

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
