FROM php:8.4-fpm-bullseye

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libmemcached-dev \
        zlib1g-dev \
        libzip-dev \
        libssl-dev \
        pkg-config \
        memcached \
        && pecl install redis memcached && \
        docker-php-ext-enable redis memcached && \
    rm -rf /var/lib/apt/lists/*

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /app

VOLUME /app

CMD ["bash", "-c", "memcached -u root -d && php-fpm"]