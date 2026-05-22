FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        ca-certificates \
        curl \
        libonig-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j$(nproc) mbstring pdo_pgsql sockets zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

RUN { \
        echo '[www]'; \
        echo 'clear_env = no'; \
    } > /usr/local/etc/php-fpm.d/zz-env.conf

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
