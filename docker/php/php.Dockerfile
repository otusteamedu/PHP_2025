FROM php:8.3-fpm-alpine

# Устанавливаем нужные пакеты
RUN apk add --no-cache unzip git curl postgresql-dev zlib-dev libmemcached-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo pdo_pgsql \
 && pecl install redis memcached \
 && docker-php-ext-enable redis memcached \
 && apk del $PHPIZE_DEPS

# Удаляем мешающий конфиг
RUN rm -f \
    /usr/local/etc/php-fpm.conf.default \
    /usr/local/etc/php-fpm.d/www.conf.default \
    /usr/local/etc/php-fpm.d/zz-docker.conf

# Копируем настройки PHP
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/local.ini

# Копируем бинарник Composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем путь Unix-сокета в контейнер
COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Копируем проект внутрь контейнера
COPY ./src /var/www/html

WORKDIR /var/www/html

# EXPOSE 9000

CMD ["php-fpm"]