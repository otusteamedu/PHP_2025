# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Задание

Необходимо реализовать Rest API с использованием очередей.

Ваши клиенты будут отправлять запросы на обработку, а вы будете складывать их в очередь и возвращать номер запроса.

В фоновом режиме вы будете обрабатывать запросы, а ваши клиенты периодически, используя номер запроса, будут проверять статус его обработки.

# Проект - Приложение верификации email

Минимальный функционал - список строк, которые необходимо проверить на наличие валидных email.
Валидация по регулярным выражениям и проверке DNS mx записи, без полноценной отправки письма-подтверждения.

# Использование проекта

1) ```docker-compose up -d --build```

2) Отправьте запрос на валидацию:
```curl -X POST -H "Content-Type: application/json" -d '{"emails": ["test@example.com", "another@test.com"]}' http://mysite.local/validate```
После чего выдаст что-то вроде: 
```{"request_id":"req_5f1a3d4b3c2a1"}```

3) Затем проверьте статус обработки, используя полученный request_id
```curl "http://mysite.local/status?request_id=req_5f1a3d4b3c2a1"```

Если обработка еще не завершена:
```
{
  "request_id": "req_5f1a3d4b3c2a1",
  "status": "pending",
  "results": []
}
```
Когда обработка завершена:
```
{
  "request_id": "req_5f1a3d4b3c2a1",
  "status": "completed",
  "results": {
    "test@example.com": {
      "email": "test@example.com",
      "isValid": true,
      "details": {
        "format": true,
        "dns": true
      }
    },
    "another@test.com": {
      "email": "another@test.com",
      "isValid": false,
      "details": {
        "format": true,
        "dns": false
      }
    }
  }
}
```

4) Для мониторинга очереди (админская функция):
```curl http://mysite.local/process-queue```

# Использование (тесты)

```docker-compose run --rm phpunit```
