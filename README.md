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

### Тестирование

Приложение покрыто Юнит, Интеграционными и Функциональными тестами с общим покрытием кода **87.12%**

#### Запуск всех тестов

```bash
docker-compose exec php composer test
```

#### Запуск тестов с покрытием кода

```bash
docker-compose exec php composer test-coverage
```

#### Запуск отдельных типов тестов

##### Юнит-тесты
```bash
docker-compose exec php composer test -- --testsuite Unit
```

##### Интеграционные тесты
```bash
docker-compose exec php composer test -- --testsuite Integration
```

##### Функциональные тесты
```bash
docker-compose exec php composer test -- --testsuite Functional
```

Для быстрого просмотра покрытия тестами:
```bash
docker-compose exec php cat coverage.txt
```

#### Статистика покрытия

| Метрика | Значение |
|---------|----------|
| **Строки кода** | 87.12% (257/295) |
| **Методы** | 92.98% (53/57) |
| **Классы** | 60.00% (6/10) |
