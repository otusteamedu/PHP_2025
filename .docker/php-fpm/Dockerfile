FROM php:8.4-fpm-alpine

COPY ./conf.d "$PHP_INI_DIR/conf.d"

RUN ln -sf /usr/share/zoneinfo/Asia/Yekaterinburg /etc/localtime  \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apk add \
      libmemcached-dev \
      cyrus-sasl-dev \
      zlib-dev \
      libevent-dev \
      build-base \
      autoconf \
      linux-headers \
      curl-dev \
      mysql-client \
      libjpeg-turbo-dev \
      libpng-dev \
      freetype-dev \
      icu-dev \
      libzip-dev \
      oniguruma-dev \
      mariadb-connector-c-dev \
    && pecl install memcache redis mysql \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable memcache redis pdo pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" \
    && php composer-setup.php --install-dir=/usr/bin \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]