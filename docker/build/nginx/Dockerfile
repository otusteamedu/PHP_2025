#
# Composer
#
FROM composer:2.8 AS composer

COPY html/ /app/

WORKDIR /app/

RUN composer install --ignore-platform-reqs --no-dev \
    && composer audit || exit 0

#
# Nginx
#
FROM nginx:1.29-trixie AS nginx

ARG UID=33
ARG GID=33

RUN set -e \
    && usermod --uid="${UID}" "www-data" \
    && groupmod --gid="${GID}" "www-data"

RUN ls -l /var/ \
    && mkdir --parents /var/www/run/ \
    && mkdir --parents /var/www/.composer/ \
    && chown --recursive www-data:www-data /var/www/

#
# Compose
#
FROM nginx AS compose

COPY docker/build/nginx/compose/etc/ /etc/

#
# Production
#
FROM nginx AS production

COPY --chown=www-data:www-data html/ /var/www/html/
COPY --chown=www-data:www-data --from=composer /app/vendor/ /var/www/html/vendor/

COPY docker/build/nginx/production/etc/ /etc/
