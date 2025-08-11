<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Iterator\OrderIterator;
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/definitions.php');
$container = $builder->build();

$orderService = $container->get('App\Service\OrderService');

try {
    $order = $orderService->createOrder([
        ['type' => 'burger', 'additives' => ['lettuce', 'cheese', 'onion']],
        ['type' => 'pizza', 'additives' => ['pepperoni', 'mushrooms']]
    ]);

    $items = $order->getItems();
    foreach ($items as $item) {
        echo $item->getName() . PHP_EOL;
        echo $item->getName() . PHP_EOL;
        echo $item->getPrice() . PHP_EOL;
        echo $item->getPrice() . PHP_EOL;
        print_r($item->getIngredients());
        print_r($item->getIngredients());
    }

    $iterator = new OrderIterator($order);
    foreach ($iterator as $status) {
        echo "Статус заказа: $status\n";
        sleep(1);
    }

    echo 'Заказ выполнен. Стоимость: ' . $order->getTotal() . " руб.\n";
} catch (Exception $e) {
    echo 'Не удалось выполнить заказ: ' . $e->getMessage();
}

