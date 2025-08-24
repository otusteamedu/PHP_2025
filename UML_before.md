# Простая UML-схема "ДО" рефакторинга

## Диаграмма классов

```mermaid
classDiagram
    class EmailValidationController {
        -validator: EmailValidatorInterface
        +handle(): string
        -getRawInput(): string
        -parseEmails(): array
        -formatResults(): string
        -formatUsageHint(): string
    }

    class EmailValidatorInterface {
        <<interface>>
        +verify(email): bool
        +verifyList(emails): array
    }

    class EmailValidator {
        -formatValidator: EmailFormatValidator
        -dnsValidator: DnsMxValidator
        +verify(email): bool
        +verifyList(emails): array
    }

    class EmailFormatValidator {
        -EMAIL_REGEX: string
        +isValid(email): bool
    }

    class DnsMxValidator {
        +isValid(email): bool
    }

    EmailValidationController --> EmailValidatorInterface
    EmailValidator ..|> EmailValidatorInterface
    EmailValidator --> EmailFormatValidator
    EmailValidator --> DnsMxValidator
```

## Проблемы исходной архитектуры:

1. **EmailValidator создает зависимости внутри себя** (нарушение DIP)
2. **Контроллер смешивает ответственности** (парсинг, валидация, форматирование)
3. **Нет четкого разделения на слои**
4. **Жесткая связанность** - сложно добавить новые валидаторы
5. **Отсутствие доменных сущностей**
