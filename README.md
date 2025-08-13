# Архитектура кода

Взята домашняя работа по теме Redis.  
Первая версия кода https://github.com/otusteamedu/PHP_2025/tree/ABurnysheva/hw10

## Первая версия приложения
```mermaid
flowchart TD
    Controllers:::bar
    classDef bar font-weight:bold,font-size:20px;
```

```mermaid
classDiagram
    class HomeController {
        +ServiceInterface service
        +add() Response
        +answer() Response
        +events() Response
        +clear() Response
    }
```

```mermaid
flowchart TD
    Services:::bar
    classDef bar font-weight:bold,font-size:20px;
```

```mermaid
classDiagram
    class MongoService {
        +Manager mongo
        +add(string $event, int $priority, array $conditions) string
        +answer(array $parameters) string
        +getEvents() string
        +clear() bool
    }
    class RedisService {
        -Redis redis
        +add(string $event, int $priority, array $conditions) string
        +answer(array $parameters) string
        +getEvents() string
        +clear() bool
    }
    class ServiceInterface {
        +add(string $event, int $priority, array $conditions) string
        +answer(array $parameters) string
        +getEvents() string
        +clear() bool
    }
```

## Вторая версия приложения

```mermaid
flowchart TD
    Domain:::bar --> Entities
    Domain:::bar --> ValueObjects
    classDef bar font-weight:bold,font-size:20px;
```

```mermaid
classDiagram
    class Event {
        +EventName eventName
        +Priority priority
        +Conditions conditions
        +getEventName()
        +getPriority()
        +getConditions()
    }
    class EventName {
        +string value
        +toString() string
    }
    class Priority {
        +int value
        +toInt() int
    }
    class Conditions {
        +array value
        +toArray() array
    }
```

```mermaid
flowchart TD
    Application:::bar --> UseCases
    UseCases --> Commands
    UseCases --> Queries
    Commands --> AddEvent
    Commands --> ClearEvents
    AddEvent --> Dto
    AddEvent --> Handler
    ClearEvents --> Handler_
    Queries --> FetchAll
    Queries --> FetchEvent
    FetchAll --> Fetcher
    FetchEvent --> Dto_
    FetchEvent --> Fetcher_
    classDef bar font-weight:bold,font-size:20px;
```

```mermaid
classDiagram
    class Dto {
        +string event
        +int priority
        +array conditions
    }
    class Handler {
        +EventRepositoryInterface repository
        +handle(Dto dto) void
    }
    class Handler_ {
        +EventRepositoryInterface repository
        +handle() void
    }
```

```mermaid
classDiagram
    class Fetcher {
        +EventRepositoryInterface repository
        +fetch() string
    }
    class Dto_ {
        +array params
    }
    class Fetcher_ {
        +EventRepositoryInterface repository
        +fetch(Dto_ dto) string
    }
```

```mermaid
classDiagram
    class EventRepositoryInterface {
        +save(Event event) void
        +fetchAll() array
        +deleteAll() void
    }
```

```mermaid
flowchart TD
    Infrastructure:::bar --> Controllers
    Infrastructure:::bar --> Repositories
    Controllers --> HomeController
    Repositories --> MongoEventRepository
    Repositories --> RedisEventRepository
    classDef bar font-weight:bold,font-size:20px;
```

```mermaid
classDiagram
    class HomeController {
        +Handler handler
        +Handler_ handler_
        +Fetcher fetcher
        +Fetcher_ fetcher_
        +add() Response
        +answer() Response
        +events() Response
        +clear() Response
    }
    class MongoEventRepository {
        +Manager mongo
        +save(Event event) void
        +fetchAll() array
        +deleteAll() void
    }
    class RedisEventRepository {
        +Redis redis
        +save(Event event) void
        +fetchAll() array
        +deleteAll() void
    }
```

## Анализ изменений
1. Более чистая архитектура кода. Выделены слои Domain, Application, Infrastructure.
2. Код лучше соотвествует принципу единственной ответственности.  
UseCases - каждая команда делает только одно действие.  
Выделены репозитории, которые занимаются только работой с хранилищем.
3. Код лучше соответствует принципу открытости-закрытости.  
Можно добавлять новые команды, новые репозитории для работы с другими хранилищами,  
не внося изменений в уже существующий код.
4. Код лучше соответствует принципу DRY, за счет выделения слоя приложения и создания общих usecases,  
которые не зависят от типа хранилища.
5. Код лучше соотвествует принципу DIP (dependency inversion principle) -  
классы зависят от абстракций (интерфейс репозитория).  
Детали (конкретные репозитории) реализуют абстракцию.
