#!/bin/sh
composer install --dev --optimize-autoloader
exec "$@"  # Передаёт управление основной команде (php-fpm)