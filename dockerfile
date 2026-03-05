FROM php:8.3-cli

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    librabbitmq-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install zip sockets

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Копируем composer файлы
COPY composer.json composer.lock* ./

# Устанавливаем зависимости
RUN composer install --no-interaction --no-scripts

# Копируем остальной код
COPY . .

# Создаем директории для логов и storage
RUN mkdir -p /app/logs /app/storage && \
    chmod 777 /app/logs /app/storage

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]