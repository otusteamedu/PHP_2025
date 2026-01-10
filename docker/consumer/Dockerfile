
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    librabbitmq-dev  \
    libssh-dev && \
    pecl install amqp && \
    docker-php-ext-enable amqp

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# User
RUN groupmod -g 1000 www-data \
  && usermod -u 1000 -g 1000 www-data

User www-data

WORKDIR /var/www/html
