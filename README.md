# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Домашнее задание к уроку 6. Сети, протоколы. Балансировка. Безопасность

**1. Приложение верификации email**

1.1. Реализовать приложение (сервис/функцию) для верификации email.

1.2. Реализация будет в будущем встроена в более крупное решение.

1.3. Минимальный функционал - список строк, которые необходимо проверить на наличие валидных email.

1.4. Валидация по регулярным выражениям и проверке DNS mx записи, без полноценной отправки письма-подтверждения.

**Пример входных данных:**

```
{
    "emails": [
        "test@google.com",
        "info@email.otus.ru",
        "тест@почта.рус",
        "xn--e1aybc@xn--80a1acny.xn--p1acf",
        "invalid-email",
        "тест",
        "test@google.con",
        "тест@абракадабра.рус"
    ]
}
```

**Пример ответа приложения c параметром $enableIDN = true:**

```
{
    "test@google.com": {
        "result": true
    },
    "info@email.otus.ru": {
        "result": true
    },
    "тест@почта.рус": {
        "result": true
    },
    "xn--e1aybc@xn--80a1acny.xn--p1acf": {
        "result": true
    },
    "invalid-email": {
        "result": false,
        "error": "Invalid format"
    },
    "тест": {
        "result": false,
        "error": "Invalid format"
    },
    "test@google.con": {
        "result": false,
        "error": "Invalid DNS"
    },
    "тест@абракадабра.рус": {
        "result": false,
        "error": "Invalid DNS"
    }
}
```

Пример ответа приложения c параметром $enableIDN = false:

```
{
    "test@google.com": {
        "result": true
    },
    "info@email.otus.ru": {
        "result": true
    },
    "тест@почта.рус": {
        "result": false,
        "error": "Invalid format"
    },
    "xn--e1aybc@xn--80a1acny.xn--p1acf": {
        "result": true
    },
    "invalid-email": {
        "result": false,
        "error": "Invalid format"
    },
    "тест": {
        "result": false,
        "error": "Invalid format"
    },
    "test@google.con": {
        "result": false,
        "error": "Invalid DNS"
    },
    "тест@абракадабра.рус": {
        "result": false,
        "error": "Invalid format"
    }
}
```
