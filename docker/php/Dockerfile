FROM php:8.4-fpm-bullseye

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        bcmath \
        sockets \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.10.1 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
