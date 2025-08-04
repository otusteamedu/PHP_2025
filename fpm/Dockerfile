FROM php:8.4-fpm

# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && pecl install memcache-8.2 \
        && pecl install redis-6.2.0

COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

#nginx workerы работают под пользователем nginx (101) в контейнере nginx, нужно чтобы на созданном сокете были права для данного пользователя
RUN addgroup --gid 101 --system nginx \
        && adduser --uid 101 --system --disabled-login --disabled-password --gid 101 nginx

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

#Устанавливаем composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add Composer to the PATH
ENV PATH="$PATH:/usr/local/bin"

WORKDIR /data

CMD ["php-fpm"]
