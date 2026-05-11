# Асинхронный REST API с очередью

Проект реализует REST API, где клиент:

1. Отправляет запрос на обработку.
2. Получает номер запроса (request_id).
3. Проверяет статус обработки по request_id.

Внутри используется RabbitMQ для очереди, Redis для хранения статусов и фоновый worker для обработки задач.

## Требования

- Docker
- Docker Compose

## Запуск

1. Поднять контейнеры:

Перед запуском создайте `.env` на основе примера:

```bash
copy .env.example .env
```

Затем поднимите контейнеры:

```bash
docker-compose down
docker-compose up -d --build
```

2. Установить зависимости в PHP-контейнере:

```bash
docker-compose exec php-fpm composer install
```

3. Запустить worker в отдельном терминале:

```bash
docker-compose exec php-fpm php worker.php
```

## REST API

Базовый URL:

http://localhost:8080

### 1. Создать запрос

Метод: POST

Путь: /api/requests

Пример:

```bash
curl -X POST http://localhost:8080/api/requests \
   -H "Content-Type: application/json" \
   -d '{
      "date_from": "2026-01-01",
      "date_to": "2026-01-31",
      "email": "client@example.com"
   }'
```

Успешный ответ (202):

```json
{
   "request_id": "4b68a83f3f384fb8a1794c0f89a94468",
   "status": "queued",
   "status_url": "/api/requests/4b68a83f3f384fb8a1794c0f89a94468",
   "created_at": "2026-04-27T18:23:21+00:00"
}
```

### 2. Проверить статус запроса

Метод: GET

Путь: /api/requests/{request_id}

Пример:

```bash
curl http://localhost:8080/api/requests/4b68a83f3f384fb8a1794c0f89a94468
```

Возможные статусы:

- queued
- processing
- completed
- failed

Пример ответа completed:

```json
{
   "request_id": "4b68a83f3f384fb8a1794c0f89a94468",
   "status": "completed",
   "created_at": "2026-04-27T18:23:21+00:00",
   "updated_at": "2026-04-27T18:23:27+00:00",
   "started_at": "2026-04-27T18:23:22+00:00",
   "completed_at": "2026-04-27T18:23:27+00:00",
   "result": {
      "message": "Statement generated successfully.",
      "processing_time_seconds": 5,
      "notified_email": "client@example.com",
      "finished_at": "2026-04-27T18:23:27+00:00"
   }
}
```

## Очереди

- Producer: API в src/index.php публикует задачи в RabbitMQ.
- Consumer: src/worker.php читает сообщения из очереди и обрабатывает их в фоне.
- Статусы запроса хранятся в Redis.
- При временных сбоях worker отправляет `nack` с requeue вместо безусловного `ack`.
- Worker использует реестр обработчиков типов задач. В учебной версии зарегистрирован один тип: `statement_generation`.

Ограничение учебной версии:

- Добавление новых типов задач требует регистрации нового обработчика в `createTaskHandlers()` в `src/worker.php`.

RabbitMQ management UI:

- URL: http://localhost:15672
- Login: берется из `RABBITMQ_USER` в `.env`
- Password: берется из `RABBITMQ_PASSWORD` в `.env`

## Swagger / OpenAPI

Файл спецификации:

- src/openapi.yaml

Его можно:

1. Открыть напрямую: http://localhost:8080/openapi.yaml
2. Импортировать в Swagger Editor: https://editor.swagger.io

## Структура

- src/index.php - REST API (create request, get status)
- src/worker.php - фоновый обработчик очереди
- src/Services/QueueService.php - работа с RabbitMQ
- src/Services/RequestStatusService.php - хранение и обновление статусов в Redis
- src/openapi.yaml - Swagger/OpenAPI документация
