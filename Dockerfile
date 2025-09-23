FROM php:8.2-cli

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание рабочей директории
WORKDIR /app

# Копирование файлов проекта
COPY . .

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader

# Установка прав на исполняемый файл
RUN chmod +x console.php

CMD ["php", "console.php", "list"]