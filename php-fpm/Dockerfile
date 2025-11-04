FROM php:8.4-fpm

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /app

VOLUME /app

CMD ["php-fpm"]