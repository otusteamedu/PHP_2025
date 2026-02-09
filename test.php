<?php

require_once __DIR__ . '/vendor/autoload.php';

use Restaurant\Config\ContainerConfig;
use Restaurant\Domain\Enums\IngredientType;
use Restaurant\Domain\Enums\OrderStatus;
use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Interfaces\SubjectInterface;
use Restaurant\Domain\Builders\ProductDirector;
use Restaurant\Domain\Decorators\IngredientDecorator;
use Restaurant\Application\Services\CookingService;
use Restaurant\Application\Services\OrderProcessingService;
use Restaurant\Infrastructure\PushNotificationObserver;
use Restaurant\Infrastructure\SmsObserver;

$director = ContainerConfig::get(ProductDirector::class);
$pricingService = ContainerConfig::get(PricingServiceInterface::class);

echo "=== Тест паттерна Строитель ===\n";
$standardBurger = $director->createStandardBurger();
$standardSandwich = $director->createStandardSandwich();
$standardHotdog = $director->createStandardHotdog();

echo "Стандартный бургер: {$standardBurger->getName()}, Цена: {$standardBurger->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $standardBurger->getIngredients()) . "\n\n";

echo "Стандартный сэндвич: {$standardSandwich->getName()}, Цена: {$standardSandwich->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $standardSandwich->getIngredients()) . "\n\n";

echo "Стандартный хот-дог: {$standardHotdog->getName()}, Цена: {$standardHotdog->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $standardHotdog->getIngredients()) . "\n\n";

echo "\n";

echo "=== Тест паттерна Декоратор ===\n";
$product = $director->createStandardSandwich();
echo "Исходный сэндвич: {$product->getName()}, Цена: {$product->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $product->getIngredients()) . "\n";

$saladDecorator = new IngredientDecorator($product, IngredientType::SALAD, $pricingService);
echo "Добавили салат: Цена: {$saladDecorator->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $saladDecorator->getIngredients()) . "\n";

$onionDecorator = new IngredientDecorator($saladDecorator, IngredientType::ONION, $pricingService);
echo "Добавили лук: Цена: {$onionDecorator->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $onionDecorator->getIngredients()) . "\n";

$pepperDecorator = new IngredientDecorator($onionDecorator, IngredientType::PEPPER, $pricingService);
echo "Добавили перец: Цена: {$pepperDecorator->getPrice()} руб.\n";
echo "Ингредиенты: " . implode(', ', $pepperDecorator->getIngredients()) . "\n\n";

echo "\n";

echo "=== Тест паттерна Наблюдатель ===\n";
$order = new Order(1, ContainerConfig::get(SubjectInterface::class));
$order->addItem($standardBurger, 2);
$order->addItem($pepperDecorator);

$pushObserver = new PushNotificationObserver();
$smsObserver = new SmsObserver();

$order->attach($pushObserver);
$order->attach($smsObserver);

echo "Заказ #{$order->getId()} создан, всего на сумму: {$order->getTotalPrice()} руб.\n";

foreach ([OrderStatus::COOKING, OrderStatus::READY, OrderStatus::DELIVERED] as $status)
{
    echo "Изменяем статус заказа на '{$status->value}'...\n";
    $order->setStatus($status);
}

echo "\n\n";

echo "=== Тест паттерна Фабричный метод ===\n";
$cookingService = ContainerConfig::get(CookingService::class);

echo "Приготовление заказа 1:\n";
$cookingService->cookOrder($order);

$order2 = new Order(2, ContainerConfig::get(SubjectInterface::class));
$order2->addItem($standardHotdog);
echo "\nПриготовление заказа 2:\n";
$cookingService->cookOrder($order2);

echo "\n\n";

echo "=== Тест паттерна Цепочка обязанностей ===\n";
$orderProcessingService = ContainerConfig::get(OrderProcessingService::class);

$order3 = new Order(3, ContainerConfig::get(SubjectInterface::class));
$order3->addItem($standardBurger);
$order3->addItem($standardHotdog, 2);

echo "Обработка нового заказа через цепочку обязанностей:\n";
$orderProcessingService->processOrder($order3);

echo "\n=== Общая информация о заказах ===\n";
echo "Заказ #1 статус: {$order->getStatus()->value}, количество позиций: {$order->getTotalQuantity()}\n";
echo "Заказ #2 статус: {$order2->getStatus()->value}, количество позиций: {$order2->getTotalQuantity()}\n";
echo "Заказ #3 статус: {$order3->getStatus()->value}, количество позиций: {$order3->getTotalQuantity()}\n";
echo "Заказ #1 общая стоимость: {$order->getTotalPrice()} руб.\n";
echo "Заказ #2 общая стоимость: {$order2->getTotalPrice()} руб.\n";
echo "Заказ #3 общая стоимость: {$order3->getTotalPrice()} руб.\n";

echo "\nТесты завершены!\n";
