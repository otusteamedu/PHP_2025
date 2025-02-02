# Image and version
FROM php:8.1-fpm-bullseye

# Install dependencies
RUN apt update && \
    apt install --no-install-recommends -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    libmemcached-dev \
    git \
    unzip

# Call PHP images script `docker-php-ext-install` and install language extensions
RUN docker-php-ext-install \
    mysqli \
    zip

RUN pecl install memcached redis && \ 
    docker-php-ext-enable memcached redis 

# copy the Composer PHAR from the Composer image into the PHP image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Override the default PHP configuration with custom php.ini
COPY ./overrides.ini /usr/local/etc/php/conf.d/overrides.ini

# Clean the image
RUN apt -y autoremove && \
    apt clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/temp/*
