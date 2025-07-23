# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


## Схема приложения до рефакторинга
```mermaid
classDiagram
    direction TB

    class BracketsValidatorApp {
        +handleRequest() void
        -isBalanced(string $str) bool
        -sendResponse(int $code, string $message) void
    }

    class HTTPRequest {
        <<external>>
        +method: string
        +postParams: array
    }

    class HTTPResponse {
        <<external>>
        +setStatusCode(int $code)
        +setHeader(string $name, string $value)
        +send(string $content)
    }

    BracketsValidatorApp --> HTTPRequest : Reads
    BracketsValidatorApp --> HTTPResponse : Writes

    note for BracketsValidatorApp "Основная логика приложения:\n1. Проверяет метод запроса\n2. Валидирует входные данные\n3. Проверяет баланс скобок\n4. Отправляет ответ"
    note for HTTPRequest "Глобальные переменные:\n$_SERVER\n$_POST"
    note for HTTPResponse "Функции:\nheader()\nhttp_response_code()\necho"
```

# SOLID принципы
* Single Responsibility: Каждый класс отвечает за одну вещь
* Open/Closed: Легко расширить валидацию новыми правилами
* Liskov Substitution: Можно создать альтернативные валидаторы
* Interface Segregation: Четкие небольшие интерфейсы
* Dependency Inversion: Зависимости от абстракций
## Обработка запроса
* Класс Request - инкапсулирует данные запроса
* Класс Response - инкапсулирует формирование ответа
* Сервисный класс BracketsValidator - содержит логику валидации
## Обработка ошибок:
* Специфичные исключения для разных сценариев
* Централизованный обработчик в index.php

## Схема классов после рефакторинга 
```mermaid
classDiagram
    direction TB

    %% Интерфейсы
    class BracketsValidatorInterface {
        <<interface>>
        +validate(string $input) bool
    }

    %% Исключения
    class EmptyStringException {
        +__construct()
    }

    class InvalidBracketsException {
        +__construct(string $message)
    }

    EmptyStringException --|> InvalidArgumentException
    InvalidBracketsException --|> InvalidArgumentException

    %% Основные классы
    class BracketsValidator {
        -ALLOWED_CHARS : array
        +validate(string $input) bool
        -validateNotEmpty(string $input) void
        -validateChars(string $input) void
        -isBalanced(string $input) bool
    }

    class Request {
        +getMethod() string
        +getPostParam(string $key, mixed $default) mixed
    }

    class Response {
        -statusCode : int
        -content : string
        -headers : array
        +setStatusCode(int $code) self
        +setContent(string $content) self
        +addHeader(string $name, string $value) self
        +send() void
    }

    %% Взаимосвязи
    BracketsValidator ..|> BracketsValidatorInterface : implements
    BracketsValidator --> EmptyStringException : throws
    BracketsValidator --> InvalidBracketsException : throws

    %% index.php зависимости
    index.php --> Request : uses
    index.php --> Response : uses
    index.php --> BracketsValidator : uses
    index.php --> EmptyStringException : catches
    index.php --> InvalidBracketsException : catches

    %% Стили для наглядности
    note for BracketsValidator "Реализует логику проверки:\n1. Непустая строка\n2. Только разрешенные символы\n3. Сбалансированность скобок"
    note for Request "Инкапсулирует доступ\nк данным HTTP-запроса"
    note for Response "Предоставляет fluent-интерфейс\nдля формирования ответа"
```
