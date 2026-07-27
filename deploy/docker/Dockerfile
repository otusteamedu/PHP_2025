FROM php:8.5-fpm-trixie

COPY --from=composer:2.8.1 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && \
    apt-get install -y unzip git libzip-dev && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*