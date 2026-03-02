#
# PHP
#
FROM php:8.4-fpm-trixie AS php

ARG UID=33
ARG GID=33

RUN apt-get update \
    && apt-get install --yes --no-install-recommends nano unzip p7zip-full procps \
    && apt-get clean && apt-get autoclean && apt-get autoremove --purge --yes

RUN set -e \
    && usermod --uid="${UID}" "www-data" \
    && groupmod --gid="${GID}" "www-data"

RUN ls -l /var/www/ \
    && mkdir --parents /var/www/run/ \
    && mkdir --parents /var/www/.composer/ \
    && chown --recursive www-data:www-data /var/www/

WORKDIR /var/www/html/

#
# Compose
#
FROM php AS compose

RUN apt-get update \
    && apt-get install --yes --no-install-recommends git \
    && apt-get clean && apt-get autoclean && apt-get autoremove --purge --yes

RUN php --modules \
    && docker-php-ext-install sockets \
    && docker-php-ext-enable sockets \
    && pecl install xdebug-3.4.7 \
    && docker-php-ext-enable xdebug

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

COPY docker/build/php/compose/usr/ /usr/

USER www-data

#
# Composer
#
FROM composer:2.8 AS composer

COPY html/ /app/

WORKDIR /app/

RUN composer install --ignore-platform-reqs --no-dev \
    && composer audit || exit 0

#
# Production
#
FROM php AS production

COPY --chown=www-data:www-data html/ /var/www/html/
COPY --chown=www-data:www-data --from=composer /app/vendor/ /var/www/html/vendor/

COPY docker/build/php/production/usr/ /usr/

USER www-data
