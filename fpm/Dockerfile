FROM php:8.2-fpm

#   устанавливаем nano для удобства просмотра конфигов
RUN apt-get update && apt-get install nano -y

#   пример установки дополнительных библиотек и модулей
#   в текущем виде - необязательно, но иметь в виду нужно
#RUN apt-get install -y \
#        curl \
#        wget \
#        git \
#        libfreetype6-dev \
#        libjpeg62-turbo-dev \
#        libpng-dev \
#        libonig-dev \
#        libzip-dev \
#        libmemcached-dev \
#        libmcrypt-dev \
#        libpq-dev \
#        && pecl install mcrypt-1.0.3 \
#        && docker-php-ext-enable mcrypt \
#        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
#        && docker-php-ext-configure gd --with-freetype --with-jpeg \
#        && docker-php-ext-install -j$(nproc) gd \
#        && docker-php-ext-install pdo pdo_pgsql
#        && pecl install memcached && \
#        docker-php-ext-enable memcached

#   копируем дополнительные настройки php при необходимости, например, настройки memcached
#COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
