FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    curl \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www/public

EXPOSE 9000

CMD ["php-fpm"]