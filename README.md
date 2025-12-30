# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# TODO

- [x] Build
- [x] Docker
- [x] Composer
- [x] ENV
- [x] Namespace
- [] Redis
- [] Memcached
- [] Console
- [] Push
- [] Search

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
