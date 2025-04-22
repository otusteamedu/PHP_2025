FROM php:8.4-fpm

RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
		git \
        curl \
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
        && rm -rf /tmp/pear \
    && docker-php-ext-enable \
        apcu \

COPY php.ini /usr/local/etc/php/php.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN mkdir -p /var/www/.composer && chown -R www-data /var/www/.composer
WORKDIR /app
CMD ["php-fpm"]