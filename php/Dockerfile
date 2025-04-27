
FROM php:8.1-fpm-bullseye

RUN pecl install redis && \ 
    docker-php-ext-enable redis 

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./overrides.ini /usr/local/etc/php/conf.d/overrides.ini

RUN apt -y autoremove && \
    apt clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/temp/*
