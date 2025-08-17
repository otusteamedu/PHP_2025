# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Установка приложения:

docker compose up --build

docker-compose exec php1 composer install --no-interaction --optimize-autoloader --verbose
docker-compose exec php2 composer install --no-interaction --optimize-autoloader --verbose