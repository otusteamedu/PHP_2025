<?php

require __DIR__ . './../vendor/autoload.php';

use App\Builder\HotdogBuilder;
use App\Decorator\ExtraCheeseHotdogBuilderDecorator;
use App\Restaurant;

$restaurant = new Restaurant(
    new HotdogBuilder(),
);

echo 'Строитель & Декоратор:' . "<br>";
$hotdog = $restaurant->buildHotdog();
$ingredientList = (implode(',', $hotdog->ingredientList));
echo "Базовый хот-дог: $hotdog->name - $ingredientList" . "<br>" . "<br>";

$hotdog = $restaurant->buildHotdog(ingredientList: ['cheese', 'onion']);
$ingredientList = (implode(',', $hotdog->ingredientList));
echo "Хот-дог с доп ингредиентами: $hotdog->name - $ingredientList" . "<br>" . "<br>";

$restaurant->setHotdogBuilder(new ExtraCheeseHotdogBuilderDecorator(new HotdogBuilder()));
$cheeseHotdog = $restaurant->buildHotdog();
$ingredientList = (implode(',', $cheeseHotdog->ingredientList));

echo "Сырный хот-дог: $cheeseHotdog->name - $ingredientList" . "<br>" . "<br>";

echo 'Адаптер:' . "<br>";
$pizzaCustomDescription = $restaurant->getProductCustomDescription('pizza', ['tomato', 'chile']);
echo "Пицца: $pizzaCustomDescription" . "<br>" . "<br>";
$nuggetsCustomDescription = $restaurant->getProductCustomDescription('nuggets');
echo "Наггетсы: $nuggetsCustomDescription" . "<br>" . "<br>";

echo 'Прокси:' . "<br>";
$pizza = $restaurant->buildPizza();

if ($pizza === null) {
    echo 'Не можем приготовить такую пиццу сейчас' . "<br>" . "<br>";
} else {
    $name = $pizza->getName();
    $ingredientList = (implode(',', $pizza->getIngredientList()));
    echo "Пицца: $name - $ingredientList" . "<br>" . "<br>";
}

$pizza = $restaurant->buildPizza(ingredientList: ['chile']);

if ($pizza === null) {
    echo 'Не можем приготовить такую пиццу сейчас' . "<br>" . "<br>";
} else {
    $name = $pizza->getName();
    $ingredientList = (implode(',', $pizza->getIngredientList()));
    echo "Пицца: $name - $ingredientList" . "<br>" . "<br>";
}

$pizza = $restaurant->buildPizzaWithNoCheck(ingredientList: ['chile']);

if ($pizza === null) {
    echo 'Не можем приготовить такую пиццу сейчас' . "<br>" . "<br>";
} else {
    $name = $pizza->getName();
    $ingredientList = (implode(',', $pizza->getIngredientList()));
    echo "Пицца: $name - $ingredientList" . "<br>" . "<br>";
}

$pizza = $restaurant->buildPizzaWithStatus();
$name = $pizza->getName();

echo 'Итератор:' . "<br>";
$currentStatus = $pizza->getCurrentPizzaStatus();
echo "$name: $currentStatus" . "<br>";
$pizza->movePizzaToNextStatus();
$currentStatus = $pizza->getCurrentPizzaStatus();
echo "$name: $currentStatus" . "<br>";
$pizza->movePizzaToNextStatus();
$currentStatus = $pizza->getCurrentPizzaStatus();
echo "$name: $currentStatus" . "<br>";
$pizza->movePizzaToNextStatus();
$currentStatus = $pizza->getCurrentPizzaStatus();
echo "$name: $currentStatus" . "<br>";
