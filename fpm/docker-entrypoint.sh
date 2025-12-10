#!/bin/sh
set -e

cd /var/www/php_course
echo "Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

exec "$@"
