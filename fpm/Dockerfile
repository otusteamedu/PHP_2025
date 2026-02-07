FROM php:8.4-fpm

ARG COMPOSER_VERSION=2.6.5
# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd

# Install composer
RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/usr/local/bin --version=${COMPOSER_VERSION} --filename=composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /app

VOLUME /app

CMD ["php-fpm"]
