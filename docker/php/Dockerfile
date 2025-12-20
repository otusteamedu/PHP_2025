FROM php:8.4-fpm-alpine

RUN apk add --no-cache curl \
    wget \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    bash \
    git \
    autoconf \
    linux-headers \
    g++ \
    make \
    mc \
    zip \
    libmemcached-dev \
    zlib-dev \
    && docker-php-ext-install bcmath sockets zip intl pdo pdo_pgsql \
    && pecl install redis && docker-php-ext-enable redis

RUN pecl install xdebug && docker-php-ext-enable xdebug

#RUN mkdir -p /var/log \
#	&& mkdir -p /var/www/html/var \
#	&& mkdir -p /var/www/html/public \
#    && touch /var/log/xdebug.log \
#    && chmod 777 /var/log/xdebug.log

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/app

EXPOSE 9000

CMD ["php-fpm", "-F"]
