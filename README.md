# Реализация паттерна Row Data Gateway с Lazy Load

Проект демонстрирует реализацию паттерна **Row Data Gateway** для работы с базой данных MySQL, включая поддержку **Lazy Load** для связанных данных и массового получения через коллекции.

## Структура проекта

```
PHP_2025/
├── database/
│   ├── schema.sql              # SQL схема для создания таблиц
│   ├── config.example.php       # Пример конфигурации БД
│   └── config.php               # Конфигурация БД (создайте из example)
├── src/
│   ├── Database/
│   │   └── Connection.php       # Класс подключения к БД (Singleton)
│   ├── Model/
│   │   ├── Order.php            # Row Data Gateway для заказов
│   │   └── User.php             # Модель пользователя
│   ├── Gateway/
│   │   └── OrderGateway.php     # Gateway для операций CRUD
│   ├── Collection/
│   │   └── OrderCollection.php  # Коллекция для массового получения
│   └── LazyLoader/
│       ├── LazyLoaderInterface.php
│       └── UserLazyLoader.php   # Lazy Load для пользователей
├── examples/
│   └── usage_example.php        # Примеры использования
├── tests/
│   └── OrderGatewayTest.php     # Тесты функциональности
└── composer.json
```

## Установка и настройка

### 1. Установка зависимостей

```bash
composer install
```

### 2. Настройка базы данных

1. Создайте базу данных MySQL:
```sql
CREATE DATABASE test_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Выполните SQL схему:
```bash
mysql -u root -p test_db < database/schema.sql
```

Или импортируйте через phpMyAdmin/другой клиент.

### 3. Настройка подключения

Скопируйте файл конфигурации:
```bash
cp database/config.example.php database/config.php
```

Отредактируйте `database/config.php` и укажите свои параметры подключения:
```php
return [
    'host' => 'localhost',
    'dbname' => 'test_db',
    'username' => 'your_username',
    'password' => 'your_password',
    // ...
];
```

## Использование

### Базовый пример

```php
require_once 'vendor/autoload.php';

use Igor\Test\Gateway\OrderGateway;
use Igor\Test\LazyLoader\UserLazyLoader;

// Настройка подключения (опционально, если используете config.php)
// Connection::setConfig([...]);

// Создание Gateway с Lazy Loader
$userLazyLoader = new UserLazyLoader();
$gateway = new OrderGateway(null, $userLazyLoader);

// Поиск заказа по ID
$order = $gateway->findById(1);

// Массовое получение заказов
$orders = $gateway->findAll();

// Перебор коллекции
foreach ($orders as $order) {
    echo "Заказ #{$order->getId()}: {$order->getTotal()} руб.\n";
    
    // Lazy Load пользователя (загружается только при первом обращении)
    $user = $order->getUser();
    if ($user) {
        echo "Пользователь: {$user->getName()}\n";
    }
}
```

### Запуск примеров

```bash
php examples/usage_example.php
```

### Запуск тестов

```bash
php tests/OrderGatewayTest.php
```

## Реализованные паттерны

### 1. Row Data Gateway

Каждый объект `Order` представляет одну строку таблицы `orders`. Объект инкапсулирует:
- Данные строки (id, user_id, total, status, created_at)
- Состояние (isNew, isDirty)
- Методы для работы с данными

**Ключевые особенности:**
- Объект не содержит логику работы с БД (это делает Gateway)
- Отслеживание изменений (dirty tracking)
- Геттеры и сеттеры для безопасного доступа к данным

### 2. Массовое получение данных

Класс `OrderCollection` реализует:
- Интерфейс `Iterator` для перебора через `foreach`
- Интерфейс `Countable` для подсчета элементов
- Методы `filter()`, `find()`, `map()` для работы с коллекцией
- Метод `toArray()` для преобразования в массив

**Пример:**
```php
$orders = $gateway->findAll(['status' => 'completed']);
echo "Найдено: " . $orders->count() . " заказов\n";

// Фильтрация
$highValueOrders = $orders->filter(function($order) {
    return $order->getTotal() > 1000;
});
```

### 3. Lazy Load

Связанные данные (пользователь заказа) загружаются только при первом обращении:

```php
$order = $gateway->findById(1);

// Пользователь еще НЕ загружен
if (!$order->isUserLoaded()) {
    echo "Пользователь не загружен\n";
}

// Первое обращение - происходит запрос к БД
$user = $order->getUser();

// Второе обращение - пользователь уже в памяти
$user2 = $order->getUser(); // Запрос к БД не выполняется
```

**Преимущества:**
- Экономия ресурсов (не загружаем данные, которые не используются)
- Оптимизация запросов
- Прозрачность для клиентского кода

**Identity Map:** `UserLazyLoader` использует простой кэш для предотвращения дублирования объектов пользователей.

## API Reference

### OrderGateway

- `findById(int $id): ?Order` - Поиск заказа по ID
- `findAll(array $conditions = [], string $orderBy = '', ?int $limit = null): OrderCollection` - Массовое получение
- `insert(Order $order): bool` - Вставка нового заказа
- `update(Order $order): bool` - Обновление заказа
- `save(Order $order): bool` - Сохранение (insert или update)
- `delete(Order $order): bool` - Удаление заказа
- `count(array $conditions = []): int` - Подсчет заказов

### Order (Row Data Gateway)

- `getId(): ?int` - Получить ID
- `getUserId(): int` - Получить ID пользователя
- `getTotal(): float` - Получить сумму
- `getStatus(): string` - Получить статус
- `getUser(): ?User` - Получить пользователя (Lazy Load)
- `setUserId(int $userId): void` - Установить ID пользователя
- `setTotal(float $total): void` - Установить сумму
- `setStatus(string $status): void` - Установить статус
- `isNew(): bool` - Проверка, новый ли заказ
- `isDirty(): bool` - Проверка, изменен ли заказ

### OrderCollection

- `count(): int` - Количество элементов
- `toArray(): array` - Преобразование в массив
- `filter(callable $callback): OrderCollection` - Фильтрация
- `find(callable $callback): ?Order` - Поиск первого элемента
- `map(callable $callback): array` - Применение функции к каждому элементу
- `isEmpty(): bool` - Проверка на пустоту

## Сложность операций

- **findById()**: O(1) - поиск по первичному ключу
- **findAll()**: O(n) - где n - количество записей
- **insert/update/delete**: O(1) - операции с одной записью
- **Lazy Load**: O(1) - загрузка одной связанной записи

## Требования

- PHP 7.4+
- MySQL 5.7+ или MariaDB 10.2+
- PDO extension
- Composer

## Лицензия

Учебный проект для демонстрации паттернов проектирования.
