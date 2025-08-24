# Простая UML-схема "ПОСЛЕ" рефакторинга

## Диаграмма классов

```mermaid
classDiagram
    %% Infrastructure Layer
    class EmailValidationController {
        -useCase: ValidateEmailsUseCase
        +handle(request): string
    }

    class HttpRequest {
        +getParameter(name): string
    }

    class ServiceContainer {
        -services: array
        +get(id): object
    }

    class EmailFormatValidator {
        -EMAIL_REGEX: string
        +isValid(email): bool
    }

    class DnsMxValidator {
        +isValid(email): bool
    }

    %% Application Layer
    class ValidateEmailsUseCase {
        -validationService: EmailValidationService
        -inputParser: InputParserService
        -resultFormatter: ResultFormatterService
        +execute(request): EmailValidationResponse
    }

    class EmailValidationRequest {
        -rawInput: string
        +getRawInput(): string
        +isEmpty(): bool
    }

    class EmailValidationResponse {
        -formattedResult: string
        +getFormattedResult(): string
    }

    class InputParserService {
        +parse(rawInput): array
    }

    class ResultFormatterService {
        +format(results): string
        +formatUsageHint(): string
    }

    %% Domain Layer
    class Email {
        -value: string
        -domain: string
        +getValue(): string
        +getDomain(): string
        +isEmpty(): bool
    }

    class EmailValidationService {
        -validators: array
        +addValidator(validator): self
        +validate(email): bool
        +validateList(emails): array
    }

    class ValidatorInterface {
        <<interface>>
        +isValid(email): bool
    }

    %% Relationships
    EmailValidationController --> ValidateEmailsUseCase
    EmailValidationController --> HttpRequest
    ValidateEmailsUseCase --> EmailValidationService
    ValidateEmailsUseCase --> InputParserService
    ValidateEmailsUseCase --> ResultFormatterService
    ValidateEmailsUseCase --> EmailValidationRequest
    ValidateEmailsUseCase --> EmailValidationResponse
    EmailValidationService --> ValidatorInterface
    EmailValidationService --> Email
    EmailFormatValidator ..|> ValidatorInterface
    DnsMxValidator ..|> ValidatorInterface
    ServiceContainer --> EmailValidationController
    ServiceContainer --> ValidateEmailsUseCase
    ServiceContainer --> EmailValidationService
    ServiceContainer --> InputParserService
    ServiceContainer --> ResultFormatterService
    ServiceContainer --> HttpRequest
    ServiceContainer --> EmailFormatValidator
    ServiceContainer --> DnsMxValidator
```

## Улучшения новой архитектуры:

1. **Четкое разделение на слои** (Domain, Application, Infrastructure)
2. **Доменная сущность Email** - центральный объект
3. **Use Case** - четкий сценарий использования
4. **DTO объекты** - для передачи данных между слоями
5. **DI-контейнер** - управление зависимостями
6. **Разделение ответственностей** - каждый класс имеет одну задачу
