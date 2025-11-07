FROM php:8.3-fpm

# Устанавливаем системные пакеты
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev

# Устанавливаем PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        iconv \
        mbstring \
        mysqli \
        pdo_mysql \
        zip \
        gd

# Устанавливаем Redis расширение
RUN pecl install redis && docker-php-ext-enable redis

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создаем директорию для сокета
RUN mkdir -p /var/run/php && chown www-data:www-data /var/run/php

# Копируем конфиг
COPY mysite.local.conf /usr/local/etc/php-fpm.d/zz-mysite.conf

WORKDIR /data/mysite.local

CMD ["php-fpm", "-F"]  # -F для foreground mode