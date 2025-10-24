#!/bin/bash

# Скрипт настройки окружения внутри контейнера

set -e

echo "Setting up environment..."

# Копируем environment variables в PHP-FPM pool
if [[ -f "/var/www/html/.env" ]]; then

    while IFS='=' read -r key value; do
        if [[ ! -z "$key" && "$key" != "#"* ]]; then
            value=$(echo "$value" | sed 's/[\&/]/\\&/g')
            echo "env[$key] = $value" >> /usr/local/etc/php-fpm.d/www.conf
        fi
    done < /var/www/html/.env
fi

chown -R www-data:www-data /var/www/html/storage
chmod -R 755 /var/www/html/storage

echo "Environment setup completed"