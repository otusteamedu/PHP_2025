FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    zip \
    unzip

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev && pecl install amqp && docker-php-ext-enable amqp
RUN docker-php-ext-install sockets
RUN curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/app/app