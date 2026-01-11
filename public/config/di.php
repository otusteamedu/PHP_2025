<?php declare(strict_types=1);

use Fastfood\DI\Container;
use Fastfood\Factories;
use Fastfood\Orders\Observers;
use Fastfood\Orders\States;
use Fastfood\Products\Builders;
use Fastfood\Services\MenuService;
use Fastfood\Services\OrderService;

$container = new Container();

/**
 * Регистрация сервисов
 */
// Регистрация строителей
$container->set('product.builder.burger', function($c) {
    return new Builders\BurgerBuilder();
});

$container->set('product.builder.sandwich', function($c) {
    return new Builders\SandwichBuilder();
});

$container->set('product.builder.hotdog', function($c) {
    return new Builders\HotdogBuilder();
});

// Регистрация обработчика событий
$container->set('product.event.disposal_handler', function($c) {
    return new \Fastfood\Products\Events\DisposalHandler();
});

// Регистрация фабрики
$container->set('product.factory', function($c) {
    $factory = new Factories\ProductFactory($c);
    $factory->addEventListener($c->get('product.event.disposal_handler'));
    return $factory;
});

// Регистрация наблюдателей
$container->set('order.observer.sms', function($c) {
    return new Observers\SmsNotifier();
});

$container->set('order.observer.push', function($c) {
    return new Observers\PushNotifier();
});


$container->set('order.observers', function($c) {
    return [
        $c->get('order.observer.push'),
        $c->get('order.observer.sms')
    ];
});

// Регистрация цепочки состояний
$container->set('order.state.chain', function($c) {
    $chain = new States\NewOrderHandler($c->get('product.factory'));
    $chain->setNext(new States\AcceptedHandler($c->get('product.factory')))
        ->setNext(new States\PreparingHandler())
        ->setNext(new States\ReadyHandler())
        ->setNext(new States\CompletedHandler());

    return $chain;
});

// Регистрация сервиса заказов
$container->set('order.service', function($c) {
    return new OrderService(
        $c->get('product.factory'),
        $c->get('order.state.chain'),
        $c->get('order.observers')
    );
});

// Регистрация сервиса меню заказов
$container->set('menu.service', function($c) {
    return new MenuService($c);
});