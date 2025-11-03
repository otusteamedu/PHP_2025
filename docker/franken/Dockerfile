FROM dunglas/frankenphp

# Копируем приложение
COPY --chown=www-data:www-data app /var/www/html/app
WORKDIR /var/www/html/app

# Копируем конфигурацию Caddy
COPY docker/franken/config/Caddyfile /etc/caddy/Caddyfile

# Копируем скрипт установки PHP-расширений
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Устанавливаем git
RUN apt-get update && apt-get install -y git

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем расширения PHP
RUN install-php-extensions pdo pdo_pgsql

RUN docker-php-ext-install sockets

RUN apt-get update && apt-get install -y sendmail


# Запуск FrankenPHP
CMD ["frankenphp", "run", "-c", "/etc/caddy/Caddyfile"]
EXPOSE 80
