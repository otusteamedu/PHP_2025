#!/bin/sh
set -e

# Заменяем переменные в redis.conf
envsubst < /usr/local/etc/redis/redis.conf.template > /usr/local/etc/redis/redis.conf

# Запускаем Redis
exec redis-server /usr/local/etc/redis/redis.conf