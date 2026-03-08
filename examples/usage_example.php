<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Igor\Test\Collection\OrderCollection;
use Igor\Test\Database\Connection;
use Igor\Test\Gateway\OrderGateway;
use Igor\Test\LazyLoader\UserLazyLoader;
use Igor\Test\Model\Order;

// Настройка подключения к БД
// В реальном проекте используйте config.php
Connection::setConfig([
    'host' => 'localhost',
    'dbname' => 'test_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]);

echo "=== Пример использования Row Data Gateway с Lazy Load ===\n\n";

// Создание Gateway с Lazy Loader для пользователей
$userLazyLoader = new UserLazyLoader();
$gateway = new OrderGateway(null, $userLazyLoader);

// ============================================
// 1. Массовое получение заказов (findAll)
// ============================================
echo "1. Массовое получение всех заказов:\n";
echo str_repeat("-", 50) . "\n";

$allOrders = $gateway->findAll([], 'id ASC');

echo "Найдено заказов: " . $allOrders->count() . "\n\n";

// Перебор коллекции через Iterator
foreach ($allOrders as $order) {
    echo sprintf(
        "Заказ #%d: пользователь_id=%d, сумма=%.2f, статус=%s\n",
        $order->getId(),
        $order->getUserId(),
        $order->getTotal(),
        $order->getStatus()
    );
}

echo "\n";

// ============================================
// 2. Фильтрация коллекции
// ============================================
echo "2. Фильтрация заказов по статусу 'completed':\n";
echo str_repeat("-", 50) . "\n";

$completedOrders = $allOrders->filter(function (Order $order) {
    return $order->getStatus() === 'completed';
});

echo "Завершенных заказов: " . $completedOrders->count() . "\n";
foreach ($completedOrders as $order) {
    echo "Заказ #{$order->getId()}: {$order->getTotal()} руб.\n";
}

echo "\n";

// ============================================
// 3. Поиск заказа по ID
// ============================================
echo "3. Поиск заказа по ID:\n";
echo str_repeat("-", 50) . "\n";

$order = $gateway->findById(1);
if ($order) {
    echo "Найден заказ:\n";
    echo "  ID: {$order->getId()}\n";
    echo "  Пользователь ID: {$order->getUserId()}\n";
    echo "  Сумма: {$order->getTotal()}\n";
    echo "  Статус: {$order->getStatus()}\n";
    echo "  Создан: {$order->getCreatedAt()}\n";
}

echo "\n";

// ============================================
// 4. Lazy Load - загрузка пользователя
// ============================================
echo "4. Демонстрация Lazy Load для пользователя:\n";
echo str_repeat("-", 50) . "\n";

$order = $gateway->findById(1);
if ($order) {
    echo "Заказ #{$order->getId()}\n";
    echo "Пользователь еще НЕ загружен: " . ($order->isUserLoaded() ? 'да' : 'нет') . "\n";
    
    // Первое обращение к getUser() - происходит загрузка из БД
    $user = $order->getUser();
    if ($user) {
        echo "Пользователь загружен (Lazy Load):\n";
        echo "  ID: {$user->getId()}\n";
        echo "  Имя: {$user->getName()}\n";
        echo "  Email: {$user->getEmail()}\n";
        echo "Пользователь загружен: " . ($order->isUserLoaded() ? 'да' : 'нет') . "\n";
        
        // Второе обращение - пользователь уже в памяти, запрос к БД не выполняется
        $user2 = $order->getUser();
        echo "Повторное обращение - пользователь берется из кэша\n";
    }
}

echo "\n";

// ============================================
// 5. Создание нового заказа
// ============================================
echo "5. Создание нового заказа:\n";
echo str_repeat("-", 50) . "\n";

$newOrder = new Order();
$newOrder->setUserId(1);
$newOrder->setTotal(999.99);
$newOrder->setStatus('pending');

echo "Новый заказ (еще не сохранен):\n";
echo "  isNew: " . ($newOrder->isNew() ? 'да' : 'нет') . "\n";
echo "  isDirty: " . ($newOrder->isDirty() ? 'да' : 'нет') . "\n";

// Сохранение
if ($gateway->insert($newOrder)) {
    echo "Заказ сохранен с ID: {$newOrder->getId()}\n";
    echo "  isNew: " . ($newOrder->isNew() ? 'да' : 'нет') . "\n";
    echo "  isDirty: " . ($newOrder->isDirty() ? 'да' : 'нет') . "\n";
}

echo "\n";

// ============================================
// 6. Обновление заказа
// ============================================
echo "6. Обновление заказа:\n";
echo str_repeat("-", 50) . "\n";

$order = $gateway->findById(2);
if ($order) {
    echo "Заказ до обновления:\n";
    echo "  Статус: {$order->getStatus()}\n";
    echo "  isDirty: " . ($order->isDirty() ? 'да' : 'нет') . "\n";
    
    $order->setStatus('processing');
    echo "  Статус изменен на: {$order->getStatus()}\n";
    echo "  isDirty: " . ($order->isDirty() ? 'да' : 'нет') . "\n";
    
    if ($gateway->update($order)) {
        echo "Заказ обновлен\n";
        echo "  isDirty: " . ($order->isDirty() ? 'да' : 'нет') . "\n";
    }
}

echo "\n";

// ============================================
// 7. Поиск с условиями
// ============================================
echo "7. Поиск заказов с условиями:\n";
echo str_repeat("-", 50) . "\n";

$userOrders = $gateway->findAll(['user_id' => 1], 'id DESC');
echo "Заказы пользователя #1: " . $userOrders->count() . "\n";
foreach ($userOrders as $order) {
    echo "  Заказ #{$order->getId()}: {$order->getTotal()} руб., статус: {$order->getStatus()}\n";
}

echo "\n";

// ============================================
// 8. Подсчет заказов
// ============================================
echo "8. Подсчет заказов:\n";
echo str_repeat("-", 50) . "\n";

$totalCount = $gateway->count();
$completedCount = $gateway->count(['status' => 'completed']);
$user1Count = $gateway->count(['user_id' => 1]);

echo "Всего заказов: {$totalCount}\n";
echo "Завершенных заказов: {$completedCount}\n";
echo "Заказов пользователя #1: {$user1Count}\n";

echo "\n=== Пример завершен ===\n";
