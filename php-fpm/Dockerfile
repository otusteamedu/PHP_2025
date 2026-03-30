FROM php:8.4-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH="$PATH:/usr/local/bin"

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /app

VOLUME /app

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
