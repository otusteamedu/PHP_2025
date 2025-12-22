FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка Redis
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Создание пользователя
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Копируем composer файлы
COPY --chown=www:www composer.json composer.lock* /var/www/html/

WORKDIR /var/www/html

USER root
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Копируем остальные файлы
COPY --chown=www:www . /var/www/html

# Меняем владельца vendor директории
RUN chown -R www:www /var/www/html/vendor

# Переключаемся на пользователя www
USER www

EXPOSE 9000

CMD ["php-fpm"]