# Приложение для верификации списка email адресов
Docker сборка для балансируемого кластера на основе двух nginx/php-fpm. Сессии PHP хранятся в Redis.

Приложение верификации email для верификации списка email адресов - Валидация по регулярным выражениям 
и проверке DNS mx записи, без полноценной отправки письма-подтверждения.

## Пример запроса:

```bash
curl --location 'http://localhost:8080/validate-emails' \
--header 'Content-Type: application/json' \
--data-raw '["fakemail@fake.tz", "real@email.com", "somewrongstring"]'
```

## Пример ответа:
```json
{
    "fakemail@fake.tz": {
        "is_valid_format": true,
        "is_valid_dns": false,
        "is_valid": false,
        "errors": [
            "No MX records found for domain"
        ]
    },
    "real@email.com": {
        "is_valid_format": true,
        "is_valid_dns": true,
        "is_valid": true,
        "errors": []
    },
    "somewrongstring": {
        "is_valid_format": false,
        "is_valid_dns": null,
        "is_valid": false,
        "errors": [
            "Invalid email format"
        ]
    }
}
```
