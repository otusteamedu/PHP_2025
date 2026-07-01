FROM php:8.4-fpm-bullseye

RUN apt-get update && apt-get install -y --no-install-recommends \
      pkg-config && \
      pecl install redis && \
      docker-php-ext-enable redis && \
     rm -rf /var/lib/apt/lists/*
