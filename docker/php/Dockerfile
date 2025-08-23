FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
      libzip-dev \
      curl \
  && apk add --no-cache --virtual .build-deps \
      oniguruma-dev \
      autoconf \
      gcc \
      g++ \
      make \
      linux-headers \
  && docker-php-ext-install \
      zip \
      opcache \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer \
  && composer --version \
  && apk del .build-deps

WORKDIR /var/www/html

CMD ["php-fpm"]
