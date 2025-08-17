FROM php:8.1-fpm

# Установка зависимостей и Composer
RUN apt-get update && apt-get install -y \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# Установка PHP-расширений
RUN docker-php-ext-install pdo pdo_mysql \
    && pecl install redis && docker-php-ext-enable redis

# Копирование файлов проекта
COPY composer.json /var/www/html/composer.json
COPY index.php /var/www/html/index.php
COPY src/ /var/www/html/src/

# Установка зависимостей Composer
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader --verbose || { echo "Composer install failed"; exit 1; }