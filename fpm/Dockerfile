FROM php:8.4-fpm

# Install system dependencies required for PHP extensions
RUN apt-get update && apt-get install -y \
      git \
      curl \
      libmemcached-dev \
      libsasl2-dev \
      zlib1g-dev \
      libjpeg-dev \
      libpng-dev \
      libfreetype6-dev \
      libicu-dev \
      libpq-dev \
      libcurl4-openssl-dev \
      dos2unix \
    && rm -rf /var/lib/apt/lists/*

# Install PECL extensions
RUN pecl install memcached redis pcov \
    && docker-php-ext-enable memcached redis pcov

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
      gd \
      pdo_pgsql \
      intl \
      curl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Set the working directory to where the code is mounted
WORKDIR /data/webserver

EXPOSE 9000

CMD ["php-fpm"]
