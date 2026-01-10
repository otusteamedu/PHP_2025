FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql sockets


# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for cache
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress || true

# Copy the rest of the app
COPY . .

# Install PHP deps (again, in case vendor not present)
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# Expose FPM port
EXPOSE 9000

CMD ["php-fpm"]
