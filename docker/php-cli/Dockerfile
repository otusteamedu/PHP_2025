FROM php:8.4-cli

# Install packages
RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
		git \
        curl \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) mbstring zip iconv

COPY php.ini /usr/local/etc/php/php.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN mkdir -p /var/www/.composer && chown -R www-data /var/www/.composer

