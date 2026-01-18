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

# DDL

```shell
cat html/data/ddl.sql | docker compose exec --no-TTY postgres psql --host localhost --username otus
```

# Insert

```shell
docker compose exec application php bin/console.php insert
```

# Find

```shell
docker compose exec application php bin/console.php lazy
```

# Update

```shell
docker compose exec application php bin/console.php update
```

# Find

```shell
docker compose exec application php bin/console.php lazy
```

# Delete

```shell
docker compose exec application php bin/console.php delete
```

# Find

```shell
docker compose exec application php bin/console.php lazy
```

# Lazy

```shell
docker compose exec application php bin/console.php lazy
```

# Eager

```shell
docker compose exec application php bin/console.php eager
```

# Down

```shell
docker compose down -v
```
