FROM php:8.2-fpm

RUN apt-get update && apt-get install nano git unzip -y

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY ./fpm/php.ini /usr/local/etc/php/conf.d/php-custom.ini

COPY ./fpm/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/php_course

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
