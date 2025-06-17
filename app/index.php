<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Application;
use App\Factory\BurgerFactory;
use App\Factory\PizzaFactory;
use App\Factory\HotDogFactory;
use App\Factory\SandwichFactory;
use App\Container\Container;
use App\Order\DineInOrderFactory;
use App\Order\TakeawayOrderFactory;

$container = new Container();

// регистрация режимов
$container->set('orderFactory.dinein', fn() => new DineInOrderFactory());
$container->set('orderFactory.takeaway', fn() => new TakeawayOrderFactory());

// регистрация фабрик продуктов
$container->set('foodFactory.burger', fn() => new BurgerFactory());
$container->set('foodFactory.pizza', fn() => new PizzaFactory());
$container->set('foodFactory.hotdog', fn() => new HotDogFactory());
$container->set('foodFactory.sandwich', fn() => new SandwichFactory());

$application = new Application($container);

// разбор CLI-аргументов
$args = $argv;
array_shift($args); // удаляем имя скрипта
$mode = $args[0] ?? 'dinein'; //выбор режима с собой или в зале
$product = $args[1] ?? 'burger'; //выбор продукта
$toppings = array_slice($args, 2); // выбор доп ингредиентов

$application->run($mode, $product, $toppings);
