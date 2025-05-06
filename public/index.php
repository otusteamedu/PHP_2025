<?php

use App\Core\Adapter\FastFoodItemInterface;
use App\Core\Adapter\Pizza;
use App\Core\Adapter\PizzaAdapter;
use App\Core\Adapter\PizzaInterface;
use App\Core\Factories\FastFoodFactory;
use App\Core\Factories\FastFoodFactoryInterface;
use App\Core\Container;
use App\Core\Kernel;
use App\Core\Observer\OrderNotifier;
use App\Core\Observer\SmsNotifier;
use App\Core\Router;
use App\Database\DatabaseConnection;
use App\Services\SmsService;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$container->singleton(PDO::class, fn() => DatabaseConnection::getConnection());
$container->singleton(SmsService::class, fn() => new SmsService());
$container->singleton(OrderNotifier::class, function(Container $container) {
    $notifier = new OrderNotifier();

    $notifier->attach($container->make(SmsNotifier::class));

    return $notifier;
});

$container->bind(FastFoodFactoryInterface::class, fn() => new FastFoodFactory());
$container->bind(PizzaInterface::class, fn() => new Pizza());
$container->bind(FastFoodItemInterface::class, function(Container $container) {
    return new PizzaAdapter($container->make(PizzaInterface::class));
});
$container->bind(Router::class, function(Container $container) {
    return new Router($container);
});



$kernel = new Kernel($container);
print_r($kernel->handle());