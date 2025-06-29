FROM php:8.4-cli

RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
        git \
        curl \
        libicu-dev \
        libpq-dev \
        librabbitmq-dev \
        postgresql-client \
        libzip-dev \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN set -xe \
    && docker-php-ext-configure \
        intl \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo \
        pdo_pgsql \
    && pecl install \
        apcu \
        amqp \
        && rm -rf /tmp/pear \
    && docker-php-ext-enable \
        apcu \
        amqp

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Настройка прав
RUN mkdir -p /var/www/.composer && \
    chown -R www-data:www-data /var/www/.composer
