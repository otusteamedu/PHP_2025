# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

**Описание/Пошаговая инструкция выполнения домашнего задания:**

1. Приложение верификации email


1.1. Реализовать приложение (сервис/функцию) для верификации email.

1.2. Реализация будет в будущем встроена в более крупное решение.

1.3. Минимальный функционал - список строк, которые необходимо проверить на наличие валидных email.

1.4. Валидация по регулярным выражениям и проверке DNS mx записи, без полноценной отправки письма-подтверждения.

**Пример входных данных:**

```
{
    "emails": [
        "test@нет.домена",
        "test@otus.ru",
        "test",
        "test@mail.ru",
        "тест@mail.ru",
        "тест.не.то.формат",
        "тест@пример.рф",
        "xn--e1aybc@xn--e1afmkfd.xn--p1ai"
    ]
}
```