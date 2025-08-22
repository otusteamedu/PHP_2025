<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Restaurant;
use App\Factories\BurgerFactory;
use App\Factories\SandwichFactory;
use App\Factories\HotdogFactory;
use App\Builders\BurgerBuilder;
use App\Strategies\StandardOrderStrategy;
use App\Strategies\PremiumOrderStrategy;
use App\Decorators\ExtraCheeseDecorator;
use App\Decorators\BaconDecorator;

$burgerFactory = new BurgerFactory();
$sandwichFactory = new SandwichFactory();
$hotdogFactory = new HotdogFactory();
$burgerBuilder = new BurgerBuilder();
$standardStrategy = new StandardOrderStrategy();
$premiumStrategy = new PremiumOrderStrategy();

$restaurant = new Restaurant(
    $burgerFactory,
    $sandwichFactory,
    $hotdogFactory,
    $burgerBuilder,
    $standardStrategy,
    $premiumStrategy
);

// Функция для конвертации копеек в рубли
$formatPrice = function(int $kopecks): string {
    return number_format($kopecks / 100, 2) . ' ₽';
};

//#region Тестирование паттернов

echo "1. Абстрактная фабрика:\n";
$burger = $restaurant->createBurger();
$sandwich = $restaurant->createSandwich();
$hotdog = $restaurant->createHotdog();

echo "   Бургер: {$burger->name} - {$formatPrice($burger->price)}\n";
echo "   Сэндвич: {$sandwich->name} - {$formatPrice($sandwich->price)}\n";
echo "   Хот-дог: {$hotdog->name} - {$formatPrice($hotdog->price)}\n\n";

echo "2. Строитель:\n";
$customBurger = $restaurant->createCustomBurger(['lettuce', 'tomato', 'cheese']);
echo "   Кастомный бургер: {$customBurger->name} - {$formatPrice($customBurger->price)}\n\n";

echo "3. Декоратор:\n";
$decoratedBurger = $restaurant->createDecoratedProduct(
    $burger, 
    [ExtraCheeseDecorator::class, BaconDecorator::class]
);
echo "   Декорированный бургер: {$decoratedBurger->name} - {$formatPrice($decoratedBurger->price)}\n\n";

echo "4. Адаптер:\n";
$pizza = $restaurant->createPizza(['pepperoni', 'mushrooms']);
echo "   Пицца: {$pizza->name} - {$formatPrice($pizza->price)}\n\n";

echo "5. Стратегия:\n";
$standardOrder = $restaurant->createStandardOrder();
$standardOrder->addProduct($burger)->addProduct($sandwich);
$standardResult = $standardOrder->process();
echo "   Стандартный заказ: {$standardResult['description']} - {$formatPrice($standardResult['total'])}\n";

$premiumOrder = $restaurant->createPremiumOrder();
$premiumOrder->addProduct($burger)->addProduct($sandwich)->addProduct($pizza);
$premiumResult = $premiumOrder->process();
echo "   Премиум заказ: {$premiumResult['description']} - {$formatPrice($premiumResult['total'])}\n\n";

//#endregion