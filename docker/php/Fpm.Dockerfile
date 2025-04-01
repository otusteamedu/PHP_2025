FROM php:fpm

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql

#RUN docker-php-ext-install zip
#RUN docker-php-ext-enable php8.3-pdo_pgsql

RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/app