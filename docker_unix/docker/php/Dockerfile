FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
      libzip \
      postgresql-libs \
      libmemcached-dev \
  && apk add --no-cache --virtual .build-deps \
      oniguruma-dev \
      libzip-dev \
      postgresql-dev \
      autoconf \
      gcc \
      g++ \
      make \
  && docker-php-ext-install \
      pdo \
      pdo_pgsql \
      zip \
      opcache \
  && pecl install redis \
  && pecl install memcached \
  && docker-php-ext-enable redis memcached \
  && apk del .build-deps \
  && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer \
  && composer --version

COPY www.conf /usr/local/etc/php-fpm.d/www.conf

RUN sed -i 's|^listen =.*|listen = /var/run/php/php-fpm.sock|' /usr/local/etc/php-fpm.d/zz-docker.conf

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
