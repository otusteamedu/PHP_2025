FROM php:8.4-fpm

# Add git to the list of installed packages
RUN apt-get update && apt-get install -y \
    nano \
    libpq-dev \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PDO and PDO PostgreSQL extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Redis session configuration
COPY redis-session.ini /usr/local/etc/php/conf.d/redis-session.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
