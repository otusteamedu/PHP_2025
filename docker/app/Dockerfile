FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
	libpng-dev \
	libonig-dev \
	libzip-dev \
	libmemcached-dev \
	libmcrypt-dev \
      && pecl install mcrypt-1.0.3 \
	  && docker-php-ext-enable mcrypt \
      && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
	  && docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y \
    librabbitmq-dev  \
    libssh-dev && \
    pecl install amqp && \
    docker-php-ext-enable amqp

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# User
RUN groupmod -g 1000 www-data \
  && usermod -u 1000 -g 1000 www-data

User www-data

WORKDIR /var/www/html

CMD ["php-fpm"]