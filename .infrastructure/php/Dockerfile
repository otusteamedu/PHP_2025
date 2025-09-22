FROM php:8.4.6-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p /var/run/php && chown www-data:www-data /var/run/php \
    && apk clean cache && rm -rf /var/cache/apk/*

WORKDIR /app

USER www-data

CMD ["php-fpm"]
