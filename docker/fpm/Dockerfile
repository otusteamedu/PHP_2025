FROM php:8.2-fpm

RUN apt-get update && apt-get install -y dos2unix git unzip zip && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf

# Позволяем запускать composer от root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Копируем entrypoint и даём права
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /data
VOLUME /data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]