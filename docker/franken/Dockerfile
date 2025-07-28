FROM dunglas/frankenphp

COPY --chown=www-data:www-data app /var/www/html/app

WORKDIR /var/www/html/app

# Пример запуска приложения: index.php в /app
CMD [ "php", "-S", "0.0.0.0:8080", "-t", "/var/www/html/app" ]