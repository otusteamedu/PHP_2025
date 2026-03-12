FROM php:8.4-fpm

COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libssh-dev \
    libsasl2-dev \
    libssl-dev \
    librabbitmq-dev \
    libpq-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql pdo_pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
