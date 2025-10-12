<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Order;
use App\Kitchen;
use App\Factories\ProductFactory;
use App\Observers\KitchenObserver;
use App\Observers\CustomerObserver;
use App\Decorators\SaladDecorator;
use App\Decorators\OnionDecorator;

// Инициализация
$kitchen = new Kitchen();
$factory = new ProductFactory();
$order = new Order($kitchen, $factory);

// Наблюдатели
$kitchen->addObserver(new KitchenObserver());
$kitchen->addObserver(new CustomerObserver());

// Пицца полностью интегрирована в систему
echo "=== Order: Pizza with toppings ===\n";
$order->createOrder('pizza', [
    new SaladDecorator(),
    new OnionDecorator()
]);

echo "Product: " . $kitchen->getCurrentProduct()->getDescription() . "\n";
echo "Status: " . $kitchen->getCurrentProduct()->getStatus() . "\n";
echo "Ingredients: " . implode(', ', $kitchen->getCurrentProduct()->getIngredients()) . "\n";

// Все продукты работают одинаково
echo "\n=== Order: Burger ===\n";
$order->createOrder('burger', [new OnionDecorator()]);
echo "Product: " . $kitchen->getCurrentProduct()->getDescription() . "\n";
