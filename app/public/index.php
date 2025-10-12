<?php

declare(strict_types=1);

use App\Adapter\Pizza;
use App\Adapter\PizzaAdapter;
use App\Builder\BurgerBuilder;
use App\Builder\FastFoodDirector;
use App\Decorator\OnionDecorator;
use App\Decorator\SaladDecorator;
use App\Iterator\Order;
use App\Iterator\OrderTracker;
use App\Proxy\CookingProxy;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new BurgerBuilder();
$director = new FastFoodDirector($builder);
$burger = $director->build();

$customBurger = new SaladDecorator(new OnionDecorator($burger));
$proxyBurger = new CookingProxy($customBurger);

$order = new Order();
$order->addItem($proxyBurger);
$order->addItem(new PizzaAdapter(new Pizza()));

$tracker = new OrderTracker();

foreach ($order as $item) {
    echo "Item: " . $item->getDescription() . "\n";
    echo "Price: $" . $item->getCost() . "\n";
    echo "Status: " . $tracker->getStatus()->value . "\n\n";
    $tracker->next();
}
