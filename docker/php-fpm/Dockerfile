FROM mlocati/php-extension-installer:latest AS php-extension-installer

FROM php:8.2-fpm-alpine
COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions redis pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php-fpm/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
