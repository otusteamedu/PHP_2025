FROM php:8.4-fpm

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /data

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php-custom.ini

COPY ../../composer.json ./

RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm"]