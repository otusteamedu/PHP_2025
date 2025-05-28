FROM php:8.2-fpm-alpine

RUN touch /var/log/requests.log && \
    chown www-data:www-data /var/log/requests.log && \
    chmod 644 /var/log/requests.log

RUN apk add --no-cache \
        autoconf \
        g++ \
        make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install opcache \
    && apk del autoconf g++ make

WORKDIR /var/www/html