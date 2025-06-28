<?php declare(strict_types=1);

use App\App;

require(__DIR__ . '/../vendor/autoload.php');

$app = new App();

$burger = $app->cookBurger();
echo "Заказ завершен, мы приготовили: " . $burger->getName() . PHP_EOL . PHP_EOL;

$sandwich = $app->cookSandwich();
echo "Заказ завершен, мы приготовили: " . $sandwich->getName() . PHP_EOL . PHP_EOL;

$hotDog = $app->cookHotDog();
echo "Заказ завершен, мы приготовили: " . $hotDog->getName() . PHP_EOL . PHP_EOL;
