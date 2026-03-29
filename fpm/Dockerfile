FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        pkg-config \
        build-essential \
        default-mysql-client \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip

RUN apt install -y libmemcached-dev zlib1g-dev libssl-dev && \
        pecl install memcached && docker-php-ext-enable memcached

RUN pecl install redis && docker-php-ext-enable redis

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

# Копирование Composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]


# # Установка зависимостей и расширений
# RUN apt-get update && apt-get install -y \
#         libfreetype6-dev \
#         libjpeg62-turbo-dev \
#         libpng-dev \
#         libonig-dev \
#         libzip-dev \
#         pkg-config \
#         build-essential \
#         && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip

# RUN apt install -y libmemcached-dev zlib1g-dev libssl-dev

# # Установка расширения Memcached
# RUN pecl install -f memcached-3.3.0 \
# 	&& docker-php-ext-enable memcached

# # Установка расширения Redis
# RUN pecl install redis && docker-php-ext-enable redis

# COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

# WORKDIR /data

# VOLUME /data

# CMD ["php-fpm"]
