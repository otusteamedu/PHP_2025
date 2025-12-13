#!/bin/sh
set -e

cd /data/mysite.local

composer install --no-interaction --prefer-dist --optimize-autoloader

exec php-fpm