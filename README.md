# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Composer

```shell
composer install --working-dir=html
```

# Configuring

```shell
cp .env.dist .env
```

```shell
cp html/.env.dist.php html/.env.php
```

# Up

```shell
docker compose up -d
```

# Down

```shell
docker compose down -v
```

# Init

```shell
php html/bin/console.php initialize
```

# Indexing

```shell
php html/bin/console.php indexing html/data/books.json
```

# Search

```shell
php html/bin/console.php search --title=лута --category=ротан --price=3761
```

```shell
php html/bin/console.php search --title=лута --category=ротан
```

```shell
php html/bin/console.php search --title=лута
```
