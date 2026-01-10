FROM php:8.4-fpm-alpine
LABEL authors="Artem Plekhanov"

WORKDIR /opt/app

RUN apk update && apk upgrade \
    && apk add --no-cache nano mc \
    && apk add --no-cache libldap openldap-dev \
    zip libzip-dev libffi-dev libpng-dev \
    libjpeg-turbo-dev freetype freetype-dev libpq-dev \
    libmcrypt-dev oniguruma-dev libxml2-dev openssl-dev \
    cyrus-sasl-dev curl
RUN apk add --no-cache shadow rabbitmq-c-dev rabbitmq-c oniguruma-dev

# Установка PHP расширений
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && pecl install raphf redis \
    && pecl install -o -f amqp \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip pdo ldap bcmath sockets pcntl \
    && docker-php-ext-enable raphf redis amqp sockets pcntl \
    && docker-php-source delete
RUN apk clean cache \
    && rm -rf /var/cache/apk/*

WORKDIR /opt/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Копирование файлов
# - публичная часть
COPY /public /opt/app/public
# - приложение
COPY /src /opt/app/src
# - конфигурация composer
COPY /composer.json /opt/app/composer.json
COPY /composer.lock /opt/app/composer.lock

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader
