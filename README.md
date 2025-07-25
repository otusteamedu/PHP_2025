# Домашнее задание №13 - Анализ кода

Проведен анализ кода и проведен рефакторинг с использованием принципа SOLID

## Проект до рефакторинга

### Схема

```mermaid
classDiagram
    class ConsoleApp {
        -eventManager: EventManager
        -storageType: string
        +__construct(storageType: string)
        +run(argv: array): void
        -handleAddCommand(argv: array): void
        -handleFindCommand(argv: array): void
        -showHelp(): void
    }

    class EventManager {
        -storage: StorageInterface
        +__construct(storage: StorageInterface)
        +addEvent(eventData: array): void
        +clearEvents(): void
        +findBestMatchingEvent(params: array): ?array
    }

    class RedisStorage {
        -redis: Redis
        -eventsKey: string
        +__construct(host: string, port: int)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    class MongoStorage {
        -collection: MongoDB\Collection
        +__construct(uri: string, dbName: string, collectionName: string)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    class ElasticsearchStorage {
        -client: Elastic\Elasticsearch\Client
        -indexName: string
        +__construct(hosts: array)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    ConsoleApp --> EventManager
    EventManager --> StorageInterface
    RedisStorage ..|> StorageInterface
    MongoStorage ..|> StorageInterface
    ElasticsearchStorage ..|> StorageInterface
    note for ConsoleApp "Нарушения SOLID:<br> 1. Создает хранилища напрямую <br> 2. Смешивает логику парсинга и выполнения команд <br> 3. Жестко привязан к конкретным реализациям"
```

### Проблемы

1. Нарушение SRP (Single Responsibility Principle):
    - ConsoleApp отвечает и за парсинг аргументов, и за создание хранилищ, и за выполнение команд
    - EventManager просто делегирует вызовы хранилищу без добавления значимой логики

2. Нарушение OCP (Open-Closed Principle):
    - Для добавления нового типа хранилища нужно изменять ConsoleApp
    - Логика создания хранилищ жестко закодирована

3. Нарушение DIP (Dependency Inversion Principle):
    - ConsoleApp зависит от конкретных реализаций хранилищ
    - EventManager зависит от интерфейса, что хорошо, но создание зависимостей происходит на неправильном уровне

## Проект после рефакторинга

### Схема

```mermaid
classDiagram
    class ConsoleApp {
        -eventManager: EventManager
        -commandHandler: CommandHandler
        +__construct(storageType: string)
        +run(argv: array): void
    }

    class CommandHandler {
        -eventManager: EventManager
        +__construct(eventManager: EventManager)
        +handle(args: array): void
        -handleAddCommand(args: array): void
        -handleFindCommand(args: array): void
        -showHelp(): void
    }

    class EventManager {
        -storage: StorageInterface
        +__construct(storage: StorageInterface)
        +addEvent(eventData: array): void
        +clearEvents(): void
        +findBestMatchingEvent(params: array): ?array
        -validateEvent(eventData: array): void
    }

    class StorageFactory {
        +create(storageType: string): StorageInterface
    }

    class RedisStorage {
        -redis: Redis
        -eventsKey: string
        +__construct(host: string, port: int)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    class MongoStorage {
        -collection: MongoDB\Collection
        +__construct(uri: string, dbName: string, collectionName: string)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    class ElasticsearchStorage {
        -client: Elastic\Elasticsearch\Client
        -indexName: string
        +__construct(hosts: array)
        +addEvent(event: array): void
        +clearEvents(): void
        +findMatchingEvent(matching: array): ?array
    }

    ConsoleApp --> EventManager
    ConsoleApp --> CommandHandler
    CommandHandler --> EventManager
    EventManager --> StorageInterface
    StorageFactory --> RedisStorage
    StorageFactory --> MongoStorage
    StorageFactory --> ElasticsearchStorage
    RedisStorage ..|> StorageInterface
    MongoStorage ..|> StorageInterface
    ElasticsearchStorage ..|> StorageInterface
    note for ConsoleApp "Улучшения:<br> 1. Делегирует создание хранилищ фабрике <br> 2. Выносит логику команд в отдельный класс <br> 3. Зависит только от абстракций"
    note for StorageFactory "Реализует принцип OCP:<br> новые хранилища добавляются без изменения существующего кода"
```

### Изменения

1. Создана фабрика для хранилищ `app/Storage/StorageFactory.php`
    - Результат: Логика создания хранилищ вынесена в отдельный класс, что соответствует SRP.
2. Упростил ConsoleApp `app/ConsoleApp.php`
3. Создан CommandHandler для обработки команд `app/CommandHandler.php`
    - Результат: Логика обработки команд вынесена в отдельный класс, что соответствует SRP.
4. Улучшен EventManager `app/EventManager.php`
    - Результат: Добавлена более детальная валидация событий, код стал более читаемым.

### Итог

1. Single Responsibility Principle:
    - Каждый класс отвечает за одну конкретную задачу
    - Логика разделена между классами более четко

2. Open-Closed Principle:
   - Для добавления нового типа хранилища нужно только добавить его в StorageFactory 
   - Остальной код не требует изменений

3. Dependency Inversion Principle:
   - Высокоуровневые модули зависят от абстракций (StorageInterface)
   - Создание конкретных реализаций вынесено в фабрику

4. Гибкость:
   - Легче добавлять новые команды или изменять существующие 
   - Проще менять реализацию отдельных компонентов