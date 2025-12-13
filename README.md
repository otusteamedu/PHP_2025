# Email Validator API

Сервис валидации email-адресов. Принимает POST-запрос с JSON, проверяет формат и наличие MX-записи.

## Запрос

**POST** `/emails` с `Content-Type: application/json`

```json
["user@example.com", "admin@mail.ru", "invalid-email"]
```

## Ответы

**Успех (200):**

```json
{
  "success": true,
  "data": [
    { "email": "user@example.com", "is_valid": true },
    { "email": "admin@mail.ru", "is_valid": true },
    { "email": "invalid-email", "is_valid": false }
  ]
}
```

**Ошибка — неверный формат (400):**

```json
{
  "success": false,
  "error": "Необходимо передать массив email-адресов в формате JSON."
}
```

**Ошибка — не POST (405):**

```json
{ "success": false, "error": "Метод не разрешен. Используйте POST запрос." }
```

**Ошибка — неверный маршрут (404):**

```json
{ "success": false, "error": "Маршрут не найден. Используйте POST /emails" }
```
