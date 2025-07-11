# Домашнее задание №11 - Паттерны работы с данными

Реализованы паттерны **DataMapper** и **Identity Map**

## Пример использования

1. Создаем подключение к БД

```php
$pdo = new PDO('pgsql:host=postgres;dbname=test', 'user', 'passwd');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

2. Создаем маппер

```php
$userMapper = new UserMapper($pdo);
```

3. Получаем всех пользователей (коллекция)

```php
$users = $userMapper->findAll();
```

### Дополнительно

Получение одного пользователя (будет взят из Identity Map, если уже загружен)

```php
$user = $userMapper->findById(11);
```

Создание нового пользователя

```php
$newUser = new User(null, 'John Doe', 'john@example.com', date('Y-m-d H:i:s'));
$userMapper->save($newUser);
```

Обновление существующего пользователя

```php
$user = $userMapper->findById(1);
$user->setName('Updated Name');
$userMapper->save($user);
```