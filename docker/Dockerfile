FROM php:8.3-cli

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git unzip curl \
    && docker-php-ext-install sockets

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Открываем порт и запускаем встроенный сервер
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
