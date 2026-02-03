<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use App\Application\Services\OrderService;
use App\Application\Services\KitchenService;

// Создаем контейнер
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/services.php');
$container = $containerBuilder->build();

// Создаем заказ
$orderService = $container->get(OrderService::class);

$order = $orderService->createOrder([
    [
        'type' => 'burger',
        'ingredients' => ['Бекон', 'Дополнительный сыр']
    ],
    [
        'type' => 'hotdog',
        'ingredients' => ['Маринованные огурчики']
    ]
], 'customer@mail.ru');

echo "Заказ создан: {$order->getId()}\n";
echo "Общая стоимость: " . number_format($order->getTotalPrice(), 0, '', ' ') . " руб.\n\n";

// Готовим заказ
$kitchenService = $container->get(KitchenService::class);
$kitchenService->prepareOrder($order);

echo "\nСтатус заказа: " . $order->getStatus()->value . "\n";

// Вывод информации о продуктах
foreach ($order->getProducts() as $product) {
    echo "\nПродукт: {$product->getName()}\n";
    echo "Цена: " . number_format($product->getPrice(), 0, '', ' ') . " руб.\n";
    echo "Статус: " . $product->getStatus()->value . "\n";
    echo "Ингредиенты: " . implode(', ', $product->getIngredients()) . "\n";
}