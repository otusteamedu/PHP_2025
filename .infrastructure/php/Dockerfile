FROM php:8.4.6-fpm-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p /var/run/php && chown www-data:www-data /var/run/php \
    && apk clean cache && rm -rf /var/cache/apk/*

WORKDIR /app

USER www-data

CMD ["php-fpm"]
