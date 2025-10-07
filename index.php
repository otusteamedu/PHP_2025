<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Order;
use App\Kitchen;
use App\Factories\ProductFactory;
use App\Observers\KitchenObserver;
use App\Observers\CustomerObserver;
use App\Decorators\SaladDecorator;
use App\Decorators\OnionDecorator;
use App\Decorators\PepperDecorator;
use App\Adapters\PizzaAdapter;
use App\Products\Pizza;

// Инициализация зависимостей через DI
$kitchen = new Kitchen();
$factory = new ProductFactory();
$order = new Order($kitchen, $factory);

// Добавляем наблюдателей 
$kitchenObserver = new KitchenObserver();
$customerObserver = new CustomerObserver();
$kitchen->addObserver($kitchenObserver);
$kitchen->addObserver($customerObserver);

// Пример заказа бургера с добавками
echo "=== Order 1: Custom Burger ===\n";
$order->createOrder('burger', [
    new SaladDecorator(),
    new OnionDecorator(),
    new PepperDecorator()
]);

echo "\nProduct: " . $kitchen->getCurrentProduct()->getDescription() . "\n";

// Пример заказа сэндвича
echo "\n=== Order 2: Simple Sandwich ===\n";
$order->createOrder('sandwich');

echo "\nProduct: " . $kitchen->getCurrentProduct()->getDescription() . "\n";

// Пример с адаптером пиццы
echo "\n=== Order 3: Pizza (via Adapter) ===\n";
$pizza = new Pizza('Margherita');
$pizzaAdapter = new PizzaAdapter($pizza);
echo $pizzaAdapter->prepare() . "\n";
echo "Ingredients: " . implode(', ', $pizzaAdapter->getIngredients()) . "\n";