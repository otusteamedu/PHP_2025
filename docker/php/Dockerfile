FROM php:8.4-fpm-alpine

RUN apk add --no-cache libzip

RUN apk add --no-cache --virtual .build-deps \
      oniguruma-dev \
      libzip-dev \
      autoconf \
      gcc \
      g++ \
      make \
 && docker-php-ext-install \
      opcache \
      zip \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .build-deps

COPY php-redis.ini /usr/local/etc/php/conf.d/

RUN curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer \
 && composer --version

WORKDIR /var/www/html

CMD ["php-fpm"]
