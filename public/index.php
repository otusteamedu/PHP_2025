<?php

use App\Core\Container;
use App\Core\Kernel;
use App\Core\Router;
use App\Observer\OrderNotifier;
use App\Observer\SmsNotifier;
use Domain\Factories\FastFoodFactory;
use Domain\Factories\FastFoodFactoryInterface;
use Infrastructure\Adapter\FastFoodItemInterface;
use Infrastructure\Adapter\Pizza;
use Infrastructure\Adapter\PizzaAdapter;
use Infrastructure\Adapter\PizzaInterface;
use Infrastructure\Database\DatabaseConnection;
use Infrastructure\Services\SmsService;

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
$container->bind('swagger', function() {
    $openapi = \OpenApi\Generator::scan([__DIR__ . '/../app', __DIR__ . '/../domain']);
    return $openapi->toJson();
});

$kernel = new Kernel($container);
print_r($kernel->handle());

