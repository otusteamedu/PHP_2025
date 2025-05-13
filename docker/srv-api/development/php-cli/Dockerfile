FROM php:8.3-alpine

RUN apk update && apk add autoconf postgresql-dev linux-headers
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql pdo pdo_mysql pcntl sockets

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
COPY --from=composer /usr/bin/composer /usr/bin/composer


WORKDIR /app
