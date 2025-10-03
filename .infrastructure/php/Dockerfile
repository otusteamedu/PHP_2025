FROM php:8.4.13-fpm-alpine

RUN apk add --update --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf make g++ linux-headers \
        && pecl install xdebug \
        && docker-php-ext-enable xdebug \
        && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p /var/run/php && chown www-data:www-data /var/run/php \
    && apk clean cache && rm -rf /var/cache/apk/*

WORKDIR /app

USER www-data

CMD ["php-fpm"]
