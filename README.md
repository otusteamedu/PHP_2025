# Book Shop Search

Консольное PHP-приложение для поиска по книжному интернет-магазину с использованием Elasticsearch.

## Требования

- PHP 8.4+
- Elasticsearch 7.x или 8.x

## Использование

### Инициализация индекса

Создаёт индекс с русским анализатором и загружает данные из books.json:

```bash
php bin/console --init
```

### Поиск книг

Простой поиск:
```bash
php bin/console --query "рыцОри"
```

С фильтрами:
```bash
php bin/console --query "рыцОри" --category "Исторический роман" --max-price 2000 --in-stock
```

Только фильтры (без поисковой строки):
```bash
php bin/console --category "Исторический роман" --max-price 2000 --in-stock
```

## Параметры

| Параметр | Описание |
|----------|-----------|
| `--host` | Хост Elasticsearch (по умолчанию: localhost) |
| `--init` | Создать индекс и загрузить данные |
| `--query` | Поисковый запрос |
| `--category` | Категория |
| `--max-price` | Максимальная цена |
| `--in-stock` | Только в наличии |
| `--help` | Справка |

## Структура

```
bin/console              - Точка входа
src/Model/Book.php      - Модель книги
src/Storage/ElasticsearchStorage.php - Слой работы с Elasticsearch
books.json             - Данные товаров
```