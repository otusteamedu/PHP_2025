FROM php:8.4-fpm

# Install system dependencies and clean up
RUN apt-get update && apt-get install --no-install-recommends -y \
        git \
        curl \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        intl \
        opcache \
        pdo \
        pdo_pgsql \
        zip \
    && pecl install \
        apcu \
    && docker-php-ext-enable \
        apcu \
    && rm -rf /tmp/pear

# Configure PHP
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/var/www/.composer

# Create working directory and set up permissions
RUN mkdir -p /var/www/.composer \
    && chown -R www-data:www-data /var/www/.composer

WORKDIR /app

# Set permissions for the working directory (after it exists)
RUN mkdir -p /app \
    && chown -R www-data:www-data /app

# Health check (optional but recommended)
HEALTHCHECK --interval=30s --timeout=3s \
    CMD php -r "exit(fastcgi_finish_request() ? 0 : 1);"

CMD ["php-fpm"]