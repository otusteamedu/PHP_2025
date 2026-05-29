# Общие зависимости для dev и prod
FROM php:8.2-fpm AS base

LABEL project=mkd-bot

RUN apt-get update && apt-get install -y libpq-dev unzip curl supervisor librabbitmq-dev \
    && docker-php-ext-install pdo_pgsql pcntl \
    && pecl install amqp && docker-php-ext-enable amqp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./fpm/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./fpm/zz-z.conf /usr/local/etc/php-fpm.d/zz-z.conf
COPY ./fpm/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./fpm/supervisor-conf.d/ /etc/supervisor/conf.d/
COPY ./fpm/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN mkdir -p /var/log/app /data/logs && \
    chown -R www-data:www-data /var/log/app /data/logs && \
    chmod -R 775 /var/log/app /data/logs

WORKDIR /data
ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Dev: pcov для покрытия тестов, код — через bind mount
FROM base AS dev

RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Builder: подготовка vendor/ для prod
FROM base AS builder

COPY ./code/ /data/
WORKDIR /data
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Test: dev-зависимости для CI
FROM base AS test

COPY ./code/ /data/
WORKDIR /data
RUN composer install --no-interaction

# Prod: код из builder, без тестов и dev-инструментов
FROM base AS prod

COPY --from=builder /data/ /data/
RUN rm -rf /data/tests /data/phpunit.xml /data/.phpunit.cache /data/.phpunit.result.cache /data/.php-cs-fixer.php /data/.php-cs-fixer.cache /data/phpstan.neon
