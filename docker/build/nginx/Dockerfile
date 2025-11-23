FROM nginx:1.29-trixie

ARG UID=33
ARG GID=33

RUN set -e \
    && usermod --uid="${UID}" "www-data" \
    && groupmod --gid="${GID}" "www-data"

RUN ls -l /var/ \
    && mkdir --parents /var/www/.composer/ \
    && chown --recursive www-data:www-data /var/www/

COPY docker/build/nginx/etc/ /etc/
