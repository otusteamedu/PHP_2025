FROM php:8.4-fpm-alpine

RUN apk add \
      libmemcached-dev \
      cyrus-sasl-dev \
      zlib-dev \
      build-base \
      autoconf \
      dos2unix \
      libjpeg-turbo-dev \
      libpng-dev \
      freetype-dev \
      icu-dev \
      bc

RUN pecl install memcached redis \
    && docker-php-ext-enable memcached redis

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
      gd \
      pdo_mysql \
      intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf


WORKDIR /data
VOLUME /data

EXPOSE 9000

CMD ["php-fpm"]