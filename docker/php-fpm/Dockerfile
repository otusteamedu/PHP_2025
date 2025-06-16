FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    libmcrypt-dev \
    libpq-dev \
    unzip \
    git\
    && pecl install redis memcached pcov \
    && docker-php-ext-install -j$(nproc) iconv mbstring pdo_mysql pdo_pgsql zip \
    && docker-php-ext-enable pdo_mysql redis memcached pcov

RUN curl -sS https://get.symfony.com/cli/installer  | bash && \
    ln -s /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /data

VOLUME /data

COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer
#RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
