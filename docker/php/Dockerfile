FROM php:8.4-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    libmemcached-dev \
    git \
    wget \
    unzip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        gd \
        bcmath \
        pcntl \
        pdo_pgsql \
        sockets \
        zip

RUN pecl channel-update pecl.php.net \
    && pecl install -o -f \
        redis \
        memcached \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable \
        redis \
        memcached

COPY docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/
COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
