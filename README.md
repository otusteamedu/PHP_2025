# Сервис хранения событий (HW#11)

HTTP-сервис для хранения и обработки событий с настраиваемым хранилищем (Redis или Memcached).

## API endpoints

### Добавить событие
- Метод: `POST`
- Путь: `/events`
- Content-Type: `application/json`
- Пример тела запроса:
```json
{
    "priority": 1000,
    "conditions": {
        "param1": 1,
        "param2": 2
    },
    "event": {
      ::event::
    }
}
```

### Получить наиболее подходящее событие
- Метод: `GET`
- Путь: `/events?action=best_match&params={"param1":1,"param2":2}`

### Получить все события
- Метод: `GET`
- Путь: `/events`

### Очистить все события
- Метод: `DELETE`
- Путь: `/events`

## Примеры

### Добавление событий

```bash
curl -X POST http://localhost/events \
  -H "Content-Type: application/json" \
  -d '{
    "priority": 1000,
    "conditions": {
      "param1": 1
    },
    "event": {
      "timestamp": "123456...",
      "message": "..."
    }
  }'
```

### Получение наиболее подходящего события

```bash
curl -X GET "http://localhost/events?action=best_match&params={\"param1\":1,\"param2\":2}"
```
