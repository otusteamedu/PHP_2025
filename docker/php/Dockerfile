FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    nano \
    librabbitmq-dev \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    opcache \
    zip \
    bcmath \
    sockets

RUN pecl install amqp && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN mkdir -p /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

CMD ["php-fpm"]