# Event Matching System

Система для хранения и поиска событий на основе критериев с использованием Redis.

## Установка и запуск

1. Клонируйте репозиторий
2. Запустите Docker Compose:
```bash
docker-compose up -d
```

### API Endpoints

Добавить событие

```bash
POST /events
Content-Type: application/json

{
    "priority": 1000,
    "conditions": {
        "param1": 1
    },
    "event": {
        "type": "user_registered",
        "message": "User registered"
    }
}
```

Найти подходящее событие
```bash
POST /query
Content-Type: application/json

{
    "params": {
        "param1": 1,
        "param2": 2
    }
}
```

Получить все события
```bash
GET /events
```

Очистить все события
```bash
DELETE /events
```

### Примеры использования

Добавление событий:
```bash
curl -X POST http://localhost:8080/events \
-H "Content-Type: application/json" \
-d '{
"priority": 1000,
"conditions": {"param1": 1},
"event": {"type": "event1", "data": "test1"}
}'
```

Поиск события:
```bash
curl -X POST http://localhost:8080/query \
  -H "Content-Type: application/json" \
  -d '{"params": {"param1": 1}}'
```