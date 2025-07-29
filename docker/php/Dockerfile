# Используем официальный образ PHP 8.4 с поддержкой FPM
FROM php:8.4-fpm

# Устанавливаем необходимые для нормальной работы модули
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /data

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php-custom.ini

CMD ["php-fpm"]