## Описание выполненного домашнего задания №20

Реализован REST API с использованием очередей для асинхронной обработки запросов

### Функциональность

- **Создание запроса**: POST `/api/requests` - клиенты отправляют запросы на обработку и получают номер запроса
- **Проверка статуса**: GET `/api/requests/{id}` - клиенты проверяют статус обработки по номеру запроса
- **Health check**: GET `/api/health` - проверка работоспособности API

### Запуск

```bash
docker-compose build && docker-compose up -d

docker-compose exec php composer install
```

### Документация

Swagger UI доступен на эндпоинте: `/api/docs`

### Качество кода

Проект использует инструменты для обеспечения качества кода:

#### PHPStan
```bash
docker-compose exec php composer analyse
```

#### PHP_CodeSniffer
```bash
docker-compose exec php composer check-style
```