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

# Build

## PHP

```shell
docker build . --file docker/build/php/Dockerfile --platform=linux/amd64 --target production --tag phexel/php-2025-10-php:latest
```

## NGINX

```shell
docker build . --file docker/build/nginx/Dockerfile --platform=linux/amd64 --target production --tag phexel/php-2025-10-nginx:latest
```

# Push

## PHP

```shell
docker push phexel/php-2025-10-php:latest
```

## NGINX

```shell
docker push phexel/php-2025-10-nginx:latest
```

# Kubernetes

## Deploy

### Secret & Infrastructure

```shell
kubectl apply -f k8s/secret.yaml
kubectl apply -f k8s/postgres.yaml
kubectl apply -f k8s/rabbitmq.yaml
```

### Migrations

```shell
kubectl delete job application-migrate
```

```shell
kubectl apply -f k8s/migration-job.yaml
```

### Application

```shell
kubectl apply -f k8s/application.yaml
```
