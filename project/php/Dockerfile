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
COPY php/composer.json /var/www/html/composer.json
COPY public/index.php /var/www/html/public/index.php
COPY php/src/ /var/www/html/src/

# Создание директории public, если не существует
RUN mkdir -p /var/www/html/public

# Установка зависимостей Composer
WORKDIR /var/www/html/
RUN composer install --no-interaction --optimize-autoloader --verbose || { echo "Composer install failed"; exit 1; }
RUN composer dump-autoload || { echo "Composer install failed"; exit 1; }

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html