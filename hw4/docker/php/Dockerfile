FROM php:7.1-fpm-alpine

# Устанавливаем redis и gettext (в нем лежит утилита envsubst)
RUN apk add --no-cache autoconf g++ make gettext \
    && pecl install redis-4.3.0 \
    && docker-php-ext-enable redis

# Копируем наш шаблон внутрь образа
COPY session.ini.template /usr/local/etc/php/conf.d/session.ini.template

# Принимаем переменную из docker-compose
ARG REDIS_PASSWORD

# Заменяем ${REDIS_PASSWORD} в шаблоне на реальное значение и создаем финальный .ini
RUN envsubst < /usr/local/etc/php/conf.d/session.ini.template > /usr/local/etc/php/conf.d/redis-session.ini \
    && rm /usr/local/etc/php/conf.d/session.ini.template

RUN apk del autoconf g++ make
