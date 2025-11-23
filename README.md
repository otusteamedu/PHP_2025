# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Configure

При необходимости создать файл ```docker-compose.override.yml```

```yaml
services:

    nginx:
        ports:
            -   target: 80
                published: 8080
```

Требуется создать файл .env

```shell
cp .env.dist .env
```

Требуется заполнить в .env :

```dotenv
POSTGRES_USER=
POSTGRES_PASSWORD=
POSTGRES_DB=
```

Требуется указать файл ```docker-compose.override.yml``` если он был создан в .env :

```shell
COMPOSE_FILE=docker-compose.yml:docker-compose.override.yml
```

# UP

```shell
docker compose build && docker compose up -d && docker compose exec php composer install
```

# cURL

```shell
curl -i -X POST "http://localhost:8080" \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "string=(()()()()))((((()()()))(()()()(((()))))))"
```
