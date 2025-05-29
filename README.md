# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Задание

Покройте юнит-тестами код
Покрытие тестами должно иметь минимальный уровень в 65%
Какие еще тесты из пирамиды тестирования могут тут быть полезными? Вы можете их реализовать?

# Использование (тесты)

```docker-compose run --rm phpunit```

# Проект - Приложение верификации email

Минимальный функционал - список строк, которые необходимо проверить на наличие валидных email.
Валидация по регулярным выражениям и проверке DNS mx записи, без полноценной отправки письма-подтверждения.

# Использование проекта

```docker-compose up -d --build```
```curl -X POST -H "Content-Type: application/json" -d '{"emails": ["test@example.com", "another@test.com"]}' mysite.local```


