FROM php:8.2-fpm

# Установка пакетов (добавлен gettext для envsubst)
RUN apt-get update && apt-get install -y \
    curl wget git libfreetype6-dev libjpeg62-turbo-dev \
    libpng-dev libonig-dev libzip-dev libpq-dev gettext \
    && rm -rf /var/lib/apt/lists/*

# Расширения PHP + Redis
RUN docker-php-ext-install -j$(nproc) \
    iconv mbstring pdo pdo_pgsql pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install redis && docker-php-ext-enable redis

# Работа с конфигами сессий
COPY session.ini.template /usr/local/etc/php/conf.d/session.ini.template
ARG REDIS_PASSWORD
RUN envsubst < /usr/local/etc/php/conf.d/session.ini.template > /usr/local/etc/php/conf.d/redis-session.ini \
    && rm /usr/local/etc/php/conf.d/session.ini.template

# Настройка сокета
RUN mkdir -p /var/run/php && chown www-data:www-data /var/run/php && chmod 777 /var/run/php

COPY php-fpm.conf /usr/local/etc/php-fpm.conf
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
CMD ["php-fpm", "-F", "-R"]
