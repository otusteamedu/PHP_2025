FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    && pecl install redis-6.0.0 \
    && docker-php-ext-enable redis \
    && docker-php-ext-install opcache
COPY index.php .