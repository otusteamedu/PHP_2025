FROM php:8.3-fpm

# Установка необходимых пакетов для Debian
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        sockets \
        mbstring \
        zip \
        opcache \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Настройка opcache
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www/html

# Создание пользователя
RUN groupadd -g 1000 app && \
    useradd -u 1000 -g app -s /bin/sh -d /var/www/html -m app && \
    chown -R app:app /var/www/html

USER app

EXPOSE 9000

CMD ["php-fpm"]