# Email Validator API

Сервис валидации email-адресов. Принимает POST-запрос с JSON, проверяет формат и наличие MX-записи.

В папке `old_code` содержится старый код с нарушениями принципов SOLID, DRY, KISS, YAGNI и без чистой архитектуры.
В папке `code` содержится исправленный код.

UML-диаграммы:

- UML_diagrams_mermaid.md
- uml_diagram_old.png
- uml_diagram_new_code.png

---

## Анализ нарушений принципов в старом коде (`old_code`)

### 1. Нарушение принципа Single Responsibility (SRP) — SOLID

**Файл:** `old_code/src/App.php`

```php
class App
{
    public function __construct()
    {
        $this->request = new Request();        // Создание зависимостей
        $this->response = new Response();      // Создание зависимостей
        $this->emailValidator = new EmailValidator(); // Создание зависимостей
    }

    public function run(): string
    {
        if (!$this->request->isPost()) { ... }           // Роутинг
        if ($this->request->getPath() !== '/emails') { ... } // Роутинг
        if (!$this->request->isValidEmailsBody()) { ... }    // Валидация входных данных
        $result = $this->emailValidator->verifyEmails($emails); // Бизнес-логика
        return $this->response->success($result);              // Формирование ответа
    }
}
```

**Проблема:** Класс `App` выполняет слишком много обязанностей:

- Создание всех зависимостей (DI Container)
- Роутинг запросов
- Валидация входных данных
- Вызов бизнес-логики
- Формирование HTTP-ответа

---

### 2. Нарушение принципа Open/Closed (OCP) — SOLID

**Файл:** `old_code/src/App.php`

```php
public function run(): string
{
    if ($this->request->getPath() !== '/emails') {
        return $this->response->error(404, 'Маршрут не найден.');
    }
    // Логика валидации email
}
```

**Проблема:** Для добавления нового маршрута (например, `/users`) необходимо изменять метод `run()`. Класс не открыт для расширения и закрыт для модификации.

---

### 3. Нарушение принципа Dependency Inversion (DIP) — SOLID

**Файл:** `old_code/src/App.php`

```php
public function __construct()
{
    $this->request = new Request();               // Жёсткая зависимость от конкретного класса
    $this->response = new Response();             // Жёсткая зависимость от конкретного класса
    $this->emailValidator = new EmailValidator(); // Жёсткая зависимость от конкретного класса
}
```

**Проблема:** Класс `App` зависит от конкретных реализаций (`Request`, `Response`, `EmailValidator`), а не от абстракций. Это делает невозможным:

- Замену реализаций (например, для тестирования)
- Мокирование зависимостей в unit-тестах
- Использование альтернативных реализаций

---

### 4. Нарушение принципа Interface Segregation (ISP) — SOLID

**Файл:** `old_code/src/Interfaces/EmailValidatorInterface.php`

```php
interface EmailValidatorInterface
{
    public function verifyEmail(string $email): bool;
    public function verifyEmails(array $emails): array;
}
```

**Проблема:** Интерфейс содержит метод `verifyEmails()`, который не является атомарной операцией валидации. Клиенты, которым нужна только проверка одного email, вынуждены зависеть от метода работы с массивами.

---

### 5. Нарушение принципа DRY (Don't Repeat Yourself)

**Файл:** `old_code/src/App.php`

```php
public function run(): string
{
    if (!$this->request->isPost()) {
        return $this->response->error(405, 'Метод не разрешен.');
    }
    if ($this->request->getPath() !== '/emails') {
        return $this->response->error(404, 'Маршрут не найден.');
    }
    if (!$this->request->isValidEmailsBody()) {
        return $this->response->error(400, 'Необходимо передать массив...');
    }
    // ...
}
```

**Проблема:** Проверки маршрутов и методов разбросаны по коду. При добавлении новых эндпоинтов придётся дублировать эту логику.

---

### 6. Нарушение принципа KISS (Keep It Simple, Stupid)

**Файл:** `old_code/src/Service/EmailValidator.php`

```php
public function verifyEmails(array $emails): array
{
    $results = [];
    foreach ($emails as $key => $email) {
        $isValid = false;
        if (is_string($email)) {
            $isValid = $this->verifyEmail($email);
        }
        $results[$key] = [
            'email' => $email,
            'is_valid' => $isValid,
        ];
    }
    return $results;
}
```

**Проблема:** Валидатор знает о формате ответа API (структура `['email' => ..., 'is_valid' => ...]`). Это смешивание ответственностей — валидатор должен только валидировать, а не формировать ответ.

---

### 7. Нарушение принципа YAGNI (You Aren't Gonna Need It)

**Файл:** `old_code/src/Http/Request.php`

```php
public function getParamFromBody(string $key): mixed
{
    return $this->body[$key] ?? null;
}
```

**Проблема:** Метод `getParamFromBody()` не используется нигде в приложении, но был добавлен "про запас". Это усложняет код и требует поддержки.

---

## Исправления в новом коде (`code`)

### 1. Исправление SRP — разделение ответственностей

**Было:** Один класс `App` делал всё.

**Стало: Ответственности разделены по слоям:**

- `Domain` — бизнес-логика, интерфейсы и DTO
- `Application` — Use Cases (бизнес-сценарии)
- `Infrastructure` — внешние зависимости (HTTP, Container)
- `Presentation` — обработка запросов (Controllers, Actions)

- Все слои зависят от Domain (внутрь)
- Domain не зависит ни от чего внешнего
- классы зависят от интерфейсов

---

### 2. Исправление OCP — паттерн Action для расширяемости

**Было:**

```php
if ($this->request->getPath() !== '/emails') { ... }
```

**Стало:**

```php
// Controller.php
foreach ($this->actions as $action) {
    if ($action->supports($request)) {
        return $action->handle($request);
    }
}
```

Теперь для добавления нового эндпоинта достаточно создать новый класс `Action` и зарегистрировать его в контейнере. Класс `Controller` изменять не нужно.

---

### 3. Исправление DIP — Dependency Injection

**Было:**

```php
$this->emailValidator = new EmailValidator();
```

**Стало:**

```php
// ValidateEmailsAction.php — зависит от Use Case через интерфейс
public function __construct(
    private readonly ValidateEmailsUseCaseInterface $validateEmailsUseCase
) {}

// ContainerBuilder.php — все зависимости регистрируются в контейнере
$container->singleton(EmailValidatorInterface::class, fn() => new EmailValidator());

$container->singleton(ValidateEmailsUseCaseInterface::class, fn(Container $c) =>
    new ValidateEmailsUseCase($c->get(EmailValidatorInterface::class))
);

$container->set(ValidateEmailsAction::class, fn(Container $c) =>
    new ValidateEmailsAction($c->get(ValidateEmailsUseCaseInterface::class))
);
```

Зависимости инжектируются через конструктор, классы зависят от интерфейсов.

---

### 4. Исправление ISP

**Было:**

```php
interface EmailValidatorInterface
{
    public function verifyEmail(string $email): bool;
    public function verifyEmails(array $emails): array;
}
```

**Стало:**

```php
interface EmailValidatorInterface
{
    public function validate(mixed $email): bool;
}
```

Интерфейс содержит только один метод атомарной валидации.

---

### 5. Исправление DRY — централизованный роутинг

**Было:** Условия роутинга в методе `run()`.

**Стало:** Каждый Action сам определяет, поддерживает ли он запрос:

```php
// ValidateEmailsAction.php
public function supports(Request $request): bool
{
    return $request->isPost() && $request->getPath() === '/emails';
}
```

---

### 6. Исправление KISS — разделение ответственностей валидатора

**Было:** `EmailValidator::verifyEmails()` формировал структуру ответа.

**Стало:** Валидатор только валидирует, Use Case оркестрирует, Action формирует ответ:

```php
// EmailValidator.php — только валидация
public function validate(mixed $email): bool { ... }

// ValidateEmailsUseCase.php — бизнес-сценарий с DTO
public function execute(EmailValidationRequest $request): array
{
    $results = [];
    foreach ($request->emails as $email) {
        $isValid = $this->emailValidator->validate($email);
        $results[] = new EmailValidationResult($email, $isValid);
    }
    return $results;
}

// ValidateEmailsAction.php — формирование HTTP-ответа
$results = $this->validateEmailsUseCase->execute($validationRequest);
$data = array_map(fn($result) => $result->toArray(), $results);
return Response::success($data);
```

---

### 7. Чистая архитектура — правильное направление зависимостей

**Было:** `ActionInterface` находился в `Domain` и зависел от `Infrastructure` (Request, Response).

**Стало:**

- `ActionInterface` перемещён в `Presentation/Interfaces/`
- Добавлены DTO в `Domain/DTO/` для передачи данных между слоями
- Добавлен слой `Application/UseCases/` с бизнес-сценариями
- Domain не зависит от внешних слоёв — все зависимости направлены внутрь

```php
// Presentation/Interfaces/ActionInterface.php — HTTP-специфичный интерфейс
interface ActionInterface
{
    public function supports(Request $request): bool;
    public function handle(Request $request): Response;
}

// Domain/DTO/EmailValidationRequest.php — DTO без внешних зависимостей
readonly class EmailValidationRequest
{
    public function __construct(public array $emails) {}
}

// Application/UseCases/ValidateEmailsUseCase.php — бизнес-сценарий
class ValidateEmailsUseCase implements ValidateEmailsUseCaseInterface
{
    public function __construct(
        private readonly EmailValidatorInterface $emailValidator
    ) {}

    public function execute(EmailValidationRequest $request): array { ... }
}
```

## API

### Запрос

**POST** `/emails` с `Content-Type: application/json`

```json
["user@example.com", "admin@mail.ru", "invalid-email"]
```

### Ответы

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

**Ошибка — не POST и не /emails (404):**

```json
{ "success": false, "error": "Not found" }
```
