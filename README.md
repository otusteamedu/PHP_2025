# PHP_2025 - Книжный поиск с Elasticsearch

Консольное PHP-приложение для поиска книг в интернет-магазине с использованием Elasticsearch.

## Возможности

- Поиск книг по названию с поддержкой опечаток (fuzzy search)
- Русская морфология для корректной работы со склонениями
- Фильтрация по категории, цене и наличию на складе
- Ранжирование результатов по релевантности

## Установка и запуск

1. Запустите Docker контейнеры:
```bash
docker compose build
docker-compose up -d
```

2. Установите зависимости Composer:
```bash
docker compose exec php composer install
```

3. Импортируйте данные книг в Elasticsearch:
```bash
docker compose exec php php import.php
```

4. Выполните поиск:
```bash
docker compose exec php php search.php --query="рыцОри" --category="Исторический роман" --max-price=2000 --in-stock
```

## Использование

### Параметры команды search.php

- `--query="<текст>"` - текст для поиска по названию (обязательно)
- `--category="<категория>"` - фильтр по категории книги
- `--max-price=<число>` - максимальная цена товара
- `--in-stock` - показать только книги в наличии

### Примеры

Поиск всех исторических романов дешевле 2000 рублей:
```bash
docker-compose exec php php search.php --query="рыцОри" --category="Исторический роман" --max-price=2000 --in-stock
```

Простой поиск по тексту:
```bash
docker-compose exec php php search.php --query="поручика"
```

Поиск только в наличии:
```bash
docker-compose exec php php search.php --category="Детектив" --in-stock
```
## Остановка

Для остановки и удаления контейнеров:
```bash
docker-compose down
```

Для удаления данных Elasticsearch:
```bash
docker-compose down -v
```
