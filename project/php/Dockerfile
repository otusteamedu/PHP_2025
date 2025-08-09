FROM php:8.1-fpm

RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install redis && docker-php-ext-enable redis

COPY index.php /var/www/html/index.php
COPY src/ /var/www/html/src/