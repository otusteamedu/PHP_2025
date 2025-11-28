#!/bin/sh
set -e

cd /app/mysite.local

echo "Running composer update..."
composer update --no-interaction

echo "Starting PHP-FPM..."
exec php-fpm