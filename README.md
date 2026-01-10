## Описание выполненного домашнего задания №10

Консольное PHP-приложение для поиска книг с использованием Elasticsearch

### Установка и запуск

#### 1. Запуск контейнеров

```bash
docker-compose build && docker-compose up -d
```

#### 2. Установка зависимостей

```bash
docker-compose exec php composer install
```

#### 3. Индексация данных

```bash
docker-compose exec php php console.php index
```

#### 4. Поиск книг

```bash
docker-compose exec php php console.php search [опции]
```

#### Примеры поиска

##### 1. Поиск с опечаткой (как в задании)
```bash
docker-compose exec php php console.php search \
  --query="рыцОри" \
  --category="Исторический роман" \
  --max-price=2000 \
  --in-stock
```

##### 2. Поиск фантастики по цене
```bash
docker-compose exec php php console.php search \
  --query="фантастика" \
  --max-price=5000 \
  --in-stock
```

#### Параметры поиска

| Параметр | Описание | Пример |
|----------|----------|---------|
| `--query` | Поисковый запрос | `"рыцарь"` |
| `--category` | Категория книг | `"Исторический роман"` |
| `--max-price` | Максимальная цена | `2000` |
| `--in-stock` | Только в наличии | (флаг) |
