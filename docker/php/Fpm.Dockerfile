FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    && curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && \
    docker-php-ext-install zip \
        pdo \
        pdo_pgsql \
        pgsql

WORKDIR /var/www/app