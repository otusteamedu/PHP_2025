#!/bin/sh
envsubst '${PHP_FPM_HOST}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf


echo "=== Generated config ==="
cat /etc/nginx/conf.d/default.conf
echo "========================"

exec "$@"