# ДЗ №17 - Практикум по тестированию
### Описание выполненного домашнего задания

Реализовано приложение для верификации e‑mail‑адресов:

- Проверка email по регулярным выражениям;
- Проверка DNS, MX-записей.

## API

Приложение предоставляет REST API для проверки email адресов.

### Endpoint

POST `/api.php`

### Request

```json
{
  "emails": [
    "test@example.com",
    "invalid.email",
    "user@gmail.com"
  ]
}
```

### Response

```json
{
  "success": true,
  "data": {
    "test@example.com": {
      "valid": false,
      "reason": "No MX records found",
      "regex_check": true,
      "mx_check": false
    },
    "invalid.email": {
      "valid": false,
      "reason": "Invalid email format",
      "regex_check": false,
      "mx_check": null
    },
    "user@gmail.com": {
      "valid": true,
      "reason": "Valid email",
      "regex_check": true,
      "mx_check": true
    }
  }
}
```

## Web Interface

Также доступен веб-интерфейс по адресу: [http://localhost/](http://localhost/)

## Запуск

1. Переименовать .env.example -> .env
2. Указать `APP_URL`
3. Запустить контейнеры:

```bash
docker-compose -f ./docker/docker-compose.yaml up -d
```

4. Затем откройте в браузере:

- API: [http://localhost/api.php](http://localhost/api.php)
- Веб-интерфейс: [http://localhost/](http://localhost/)