#
# PHP
#
FROM php:8.4-cli-trixie AS php

ARG UID=33
ARG GID=33

RUN apt-get update \
    && apt-get install --yes --no-install-recommends nano unzip p7zip-full procps \
    && apt-get clean && apt-get autoclean && apt-get autoremove --purge --yes

RUN php --modules \
    && apt-get install --yes --no-install-recommends lsb-release curl ca-certificates \
    && install --directory /usr/share/postgresql-common/pgdg \
    && curl --output /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc --fail https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    && echo "deb [signed-by=/usr/share/postgresql-common/pgdg/apt.postgresql.org.asc] https://apt.postgresql.org/pub/repos/apt $(lsb_release --codename --short)-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install --yes --no-install-recommends libpq-dev postgresql-client-18 \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql \
    && apt-get clean && apt-get autoclean && apt-get autoremove --purge --yes

RUN set -e \
    && usermod --uid="${UID}" "www-data" \
    && groupmod --gid="${GID}" "www-data"

RUN ls -l /var/www/ \
    && mkdir --parents /var/www/.composer/ \
    && chown --recursive www-data:www-data /var/www/

WORKDIR /var/www/html/

#
# Compose
#
FROM php AS compose

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

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

USER www-data
