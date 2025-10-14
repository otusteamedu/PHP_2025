<?php

declare(strict_types=1);

namespace Restaurant\Command;

use Restaurant\Builder\BurgerBuilder;
use Restaurant\Iterator\Order;
use Restaurant\Iterator\OrderIterator;
use Restaurant\Decorator\CheeseDecorator;
use Restaurant\Decorator\BaconDecorator;

readonly class ProcessOrderCommand
{
    public function __construct(private BurgerBuilder $builder)
    {
    }

    public function execute(): void
    {
        echo "=== Обработка заказа ===\n\n";

        // Создаем заказ
        $order = new Order(12345);

        // Добавляем продукты
        $burger1 = $this->builder->createBurger();
        $burger1 = new CheeseDecorator($burger1);
        $order->addProduct($burger1);

        $burger2 = $this->builder->createBurger();
        $burger2 = new BaconDecorator($burger2);
        $order->addProduct($burger2);

        echo "Заказ #{$order->getId()}\n";
        echo "Продуктов в заказе: " . count($order->getProducts()) . "\n";
        foreach ($order->getProducts() as $product) {
            echo "  - {$product->getDescription()} ({$product->getPrice()} руб.)\n";
        }
        echo "Общая стоимость: {$order->getTotalPrice()} руб.\n\n";

        // Обрабатываем заказ через итератор
        $iterator = new OrderIterator($order);

        echo "Движение заказа по статусам:\n";
        while ($iterator->hasNext()) {
            $status = $iterator->next();
            echo "  -> Статус: {$status->value}\n";
            sleep(1); // Имитация обработки
        }

        echo "\n+Заказ обработан и выдан клиенту!\n";
    }
}
