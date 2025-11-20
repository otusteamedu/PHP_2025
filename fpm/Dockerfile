FROM php:8.4-fpm

RUN apt-get update && apt-get install nano -y

# расширение Redis
RUN pecl install redis && docker-php-ext-enable redis

# конфигурации - хранения сессий в Redis
COPY redis-session.ini /usr/local/etc/php/conf.d/redis-session.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
