<?php

require 'vendor/autoload.php';

use Hafiz\Php2025\Container\AppContainer;
use Hafiz\Php2025\Factory\BurgerFactory;
use Hafiz\Php2025\Decorator\LettuceDecorator;
use Hafiz\Php2025\Decorator\OnionDecorator;
use Hafiz\Php2025\Product\PizzaAdapter;
use Hafiz\Php2025\Builder\OrderBuilder;
use Hafiz\Php2025\Product\Pizza;

$container = new AppContainer();

$container->bind(BurgerFactory::class, fn() => new BurgerFactory());
$container->bind(OrderBuilder::class, fn() => new OrderBuilder());
$container->bind(Pizza::class, fn() => new Pizza());
$container->bind(PizzaAdapter::class, fn($c) => new PizzaAdapter($c->make(Pizza::class)));

$burgerFactory = $container->make(BurgerFactory::class);
$burger = $burgerFactory->createProduct();
$burgerWithToppings = new LettuceDecorator(new OnionDecorator($burger));

$pizza = $container->make(PizzaAdapter::class);

$orderBuilder = $container->make(OrderBuilder::class);
$order = $orderBuilder
    ->addProduct($burgerWithToppings)
    ->addProduct($pizza)
    ->build();

echo "Ваш заказ:\n";
echo $order->describeOrder();
echo "\nИтого: " . $order->getTotalCost();
