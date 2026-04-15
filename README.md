# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Configure

## Docker

```shell
cp .env.dist .env
```

## Application

```shell
cp html/.env.dist.php html/.env.php
```

# Docker

## Build

```shell
docker compose build
```

## Composer install

```shell
docker compose run --rm php composer install
```

## Database migration

```shell
docker compose run --rm php php bin/console.php database:migrate
```

## Up

```shell
docker compose up -d
```

# Available

## Application

```
http://localhost:8000/
```

## Swagger

```shell
http://localhost:8080/swagger/ui
```

## RabbitMQ

```
http://localhost:15672/
```

# Advanced

## Console consumer

```shell
docker compose exec php php bin/console.php queue:consumer
```

## Console publisher

```shell
docker compose exec php php bin/console.php queue:publisher Example
```

# PHPUnit

```shell
docker compose run --rm php composer phpunit
```
