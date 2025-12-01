FROM php:8.3-fpm

# Зависимости
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    dnsutils \
    git \
    vim \
    && docker-php-ext-install mbstring xml

WORKDIR /var/www/html
