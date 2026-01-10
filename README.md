# Очередь задач: REST API на Slim + RabbitMQ + PostgreSQL

Проект реализует простое REST API для постановки задач в очередь (RabbitMQ) и получения статуса обработки. 
При обработке задачи в БД создаётся/обновляется запись с номером запроса и временем обработки.

## Стек
- PHP 8.2 (php-fpm в контейнере)
- Slim 4
- RabbitMQ 3 (в контейнере, с management UI)
- PostgreSQL (НА ХОСТЕ, вне docker-compose)
- Nginx (в контейнере)

## Быстрый старт
1) Установите PostgreSQL локально и создайте БД, например:
```
CREATE DATABASE otus_hw30;
```
2) Скопируйте файл переменных окружения и при необходимости отредактируйте:
```
cp .env.sample .env
```
По умолчанию Docker будет подключаться к PostgreSQL на хост-машине через `host.docker.internal` (актуально для Docker Desktop на Windows). При необходимости измените `PG_DSN`.

3) Поднимите контейнеры:
```
docker compose up -d --build
```
Откроется:
- API: http://localhost:8080
- RabbitMQ UI: http://localhost:15672 (guest/guest)

4) Установите зависимости PHP внутри контейнера (если не установились на этапе сборки):
```
docker compose exec php-fpm composer install --no-dev
```

5) Примените миграции (создаст таблицу `tasks`):
```
docker compose exec php-fpm php scripts/migrate.php
```

6) Запустите воркер обработки очереди:
```
docker compose exec php-fpm php worker/worker.php
```
Воркер будет слушать очередь RabbitMQ и отмечать задачи обработанными.

## Использование API
- Поставить задачу в очередь:
```
POST http://localhost:8080/tasks
Content-Type: application/json

{"payload": "любые данные"}
```
Ответ 201:
```
{"id": "<uuid>", "status": "queued"}
```

- Получить статус задачи:
```
GET http://localhost:8080/tasks/{id}
```
Ответ 200:
```
{"id":"...","payload":"...","status":"processed","processed_at":"2025-10-07T10:30:00+00:00"}
```
Если задача не найдена: 404.

## Архитектура
- public/index.php — входная точка Slim
- src/routes.php — маршруты
- src/container.php — DI-контейнер
- src/Controller/TaskController.php — контроллер API (с OpenAPI‑комментариями)
- src/Infrastructure/* — подключения к RabbitMQ и PostgreSQL
- src/Repository/TaskRepository.php — слой работы с БД
- worker/worker.php — консольный воркер-обработчик очереди
- migrations/*.sql — SQL‑миграции
- scripts/migrate.php — простой запуск миграций

## Примечания
- PostgreSQL работает на хосте, а не в Docker, согласно заданию.
- Для Windows/WSL используйте `host.docker.internal` чтобы контейнеры могли подключаться к БД хоста.
- Воркер можно запускать несколькими инстансами для параллельной обработки.
- Очередь `tasks` создаётся автоматически при первом подключении.
