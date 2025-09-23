FROM php:8.4.6-fpm-alpine

RUN apk add --update --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf make g++ linux-headers redis \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p /var/run/php && chown www-data:www-data /var/run/php \
    && apk clean cache && rm -rf /var/cache/apk/*

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app

USER www-data

CMD ["php-fpm"]
