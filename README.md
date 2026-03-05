# Async API - Асинхронная обработка задач

## Описание

API для асинхронной обработки задач с использованием очередей Redis.

## БЫСТРЫЙ ЗАПУСК

### 1. Установите Docker

**Windows:** https://www.docker.com/products/docker-desktop/  
**Mac:** `brew install --cask docker`  
**Linux:** `sudo apt install docker.io docker-compose`

### 2. Скачайте и запустите проект

```bash
# Клонируйте репозиторий
git clone <ссылка на ваш репозиторий>
cd async-api

# Запустите контейнеры
docker-compose up -d

# Проверьте что все контейнеры работают
docker-compose ps
```

### 3. Проверьте работу API

```bash
# Отправьте задачу на обработку
curl -X POST http://localhost:8080/api/submit \
  -H "Content-Type: application/json" \
  -d '{"data": "{\"text\": \"Привет, мир!\"}"}'

# Вы получите ответ с request_id (например: 1)
# Сразу проверьте статус
curl http://localhost:8080/api/status/1

# Подождите 5 секунд и проверьте снова - увидите результат!
curl http://localhost:8080/api/status/1
```

### 4. Посмотрите работу воркера (опционально)

```bash
# В отдельном терминале
docker-compose logs -f worker
```

## Эндпоинты API

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/submit` | Отправить задачу |
| GET | `/api/status/{id}` | Проверить статус |

## Примеры запросов

**POST /api/submit**
```json
{
    "data": "{\"text\": \"Hello World\"}"
}
```

**Ответ:**
```json
{
    "request_id": 1,
    "status": "pending",
    "message": "Task accepted for processing",
    "check_url": "/api/status/1"
}
```

**GET /api/status/1 (готово)**
```json
{
    "request_id": 1,
    "status": "completed",
    "created_at": "2024-01-01 12:00:00",
    "result": {
        "original": "Hello World",
        "reversed": "dlroW olleH",
        "length": 11,
        "processed_at": "2024-01-01 12:00:05"
    }
}
```

## Команды

```bash
# Остановить проект
docker-compose down

# Перезапустить проект
docker-compose restart

# Посмотреть логи всех сервисов
docker-compose logs -f

# Посмотреть логи только воркера
docker-compose logs -f worker

# Очистить всё (удалить контейнеры и БД)
docker-compose down -v
```

## Проверка

- POST /api/submit возвращает 202 и request_id
- GET /api/status/{id} показывает статус pending
- Через 5 секунд статус меняется на completed
- В логах воркера видна обработка задачи