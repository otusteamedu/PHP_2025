# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Configuring

```shell
cp .env.dist .env
```

```shell
cp html/.env.dist.php html/.env.php
```

# Build

```shell
docker compose build
```

# Composer

```shell
docker compose run --rm application composer install
```

# Up

```shell
docker compose up -d
```

# Down

```shell
docker compose down -v
```

# Push

```shell
docker compose exec application php bin/console.php push "$(cat html/data/1000.json)"
```

```shell
docker compose exec application php bin/console.php push "$(cat html/data/2000.json)"
```

```shell
docker compose exec application php bin/console.php push "$(cat html/data/3000.json)"
```

# Search

```shell
docker compose exec application php bin/console.php search "$(cat html/data/request.json)"
```
