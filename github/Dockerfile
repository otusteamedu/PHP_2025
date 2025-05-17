FROM php:8.3-alpine

# Установка необходимых пакетов
RUN apk update && apk upgrade && \
    apk add --no-cache \
    bash \
    ffmpeg \
    curl \
    unzip \
    git \
    python3 \
    py3-pip

# Установка yt-dlp
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Копирование composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка переменной окружения для composer (работа от root)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Установка рабочей директории
WORKDIR /app

# Копируем composer файлы отдельно для кеша
COPY composer.json ./

# Устанавливаем зависимости
RUN composer install --no-interaction --prefer-dist

# Копируем оставшиеся файлы проекта
COPY . .

