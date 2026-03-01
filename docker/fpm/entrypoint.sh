#!/bin/sh
set -e

cd /data/mysite.local

composer install --no-interaction --prefer-dist --optimize-autoloader

# Если передана команда - выполняем её, иначе запускаем php-fpm
if [ $# -gt 0 ]; then
    exec "$@"
else
    exec php-fpm
fi