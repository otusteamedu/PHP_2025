FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    nano \
    librabbitmq-dev \
    libzip-dev \
    libonig-dev \
    libyaml-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    opcache \
    zip \
    bcmath \
    sockets \
    pdo_mysql \
    mbstring

RUN pecl install amqp yaml && docker-php-ext-enable amqp yaml

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN mkdir -p /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

COPY . .

RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]
