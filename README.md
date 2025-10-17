# PHP_2025

Консольное приложение для поиска книг в Elasticsearch

## Запуск окружения

Запустите Docker окружение:

```bash
docker compose up -d --build
```

Создайте индекс Elasticsearch книжного магазина:

```bash
curl --location --request PUT 'https://localhost:9200/otus-shop' \
--header 'Content-Type: application/json' \
--header 'Authorization: Basic <auth>' \
--data '{
    "settings": {
        "analysis": {
            "filter": {
                "ru_stop": {
                    "type": "stop",
                    "stopwords": "_russian_"
                },
                "ru_stemmer": {
                    "type": "stemmer",
                    "language": "russian"
                }
            },
            "analyzer": {
                "my_russian": {
                    "tokenizer": "standard",
                    "filter": [
                        "lowercase",
                        "ru_stop",
                        "ru_stemmer"
                    ]
                }
            }
        }
    },
    "mappings": {
        "properties": {
            "category": {
                "type": "keyword"
            },
            "price": {
                "type": "integer"
            },
            "sku": {
                "type": "keyword"
            },
            "stock": {
                "type": "nested",
                "properties": {
                    "shop": {
                        "type": "keyword"
                    },
                    "stock": {
                        "type": "integer"
                    }
                }
            },
            "title": {
                "type": "text",
                "analyzer": "my_russian"
            }
        }
    }
}'
```

Импортируйте данные для индекса книжного магазина:

```bash
curl \
  --location \
  --insecure \
  --request POST 'https://localhost:9200/_bulk' \
  --header 'Authorization: Basic <auth>' \
  --header 'Content-Type: application/json' \
  --data-binary "@data/books.json"
```

## Установка зависимостей

В контейнере PHP выполните установку зависимостей Composer (один раз):

```bash
docker exec -it bookstore-app-php bash -lc "composer install"
```

## Конфигурация

Параметры подключения к Elasticsearch задаются в файле `.env`:

```
ELASTIC_URL=https://bookstore-app-elastic:9200/
ELASTIC_INDEX=otus-shop
ELASTIC_USERNAME=elastic
ELASTIC_PASSWORD=elastic
```

## Запуск консольной команды

Примеры запуска:

- Поиск по названию с учётом опечаток и морфологии, только в наличии, исторические романы дешевле 2000:

```bash
docker exec -it bookstore-app-php php /app/bin/search --title="рыцОри" --category="Исторический роман" --max-price=2000 --in-stock=1
```

- Позиционные аргументы (по заданию):

```bash
docker exec -it bookstore-app-php php /app/bin/search "рыцОри" "Исторический роман" 0 2000
```