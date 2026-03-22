<?php

declare(strict_types=1);

use Otus\Code\Application\Cuisine\Template\FastFoodCookingProcess;
use Otus\Code\Application\Order\Builder\OrderBuilder;
use Otus\Code\Application\Product\Decorator\CheeseDecorator;
use Otus\Code\Application\Product\Decorator\OnionDecorator;
use Otus\Code\Application\Product\Decorator\PepperDecorator;
use Otus\Code\Application\Product\Decorator\PicklesDecorator;
use Otus\Code\Application\Product\Recipe\ProductCustomizer;
use Otus\Code\Application\Product\Recipe\RecipeBook;
use Otus\Code\Application\Product\Strategy\BurgerStrategy;
use Otus\Code\Application\Product\Strategy\HotDogStrategy;
use Otus\Code\Domain\Order\Enum\OrderStatus;
use Otus\Code\Infrastructure\Notification\Channel\PushChannel;
use Otus\Code\Infrastructure\Notification\Channel\SmsChannel;
use Otus\Code\Infrastructure\Notification\Observer\PushOrderObserver;
use Otus\Code\Infrastructure\Notification\Observer\SmsOrderObserver;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$builder = new OrderBuilder(new ProductCustomizer());

$order = $builder
    ->start('ORDER-15')
    ->subscribe(new PushOrderObserver('customer-app', new PushChannel()))
    ->subscribe(new SmsOrderObserver('+79990000000', new SmsChannel()))
    ->addRecipeItem(new BurgerStrategy(), RecipeBook::classicBurger())
    ->addCustomItem(new HotDogStrategy(), [
        CheeseDecorator::class,
        PepperDecorator::class,
        PicklesDecorator::class,
        OnionDecorator::class
    ])
    ->moveTo(OrderStatus::Created)
    ->moveTo(OrderStatus::Cooking)
    ->build();

$cuisine = new FastFoodCookingProcess();

echo "Order summary\n";
echo 'ID: ' . $order->getId() . "\n";
echo 'Status: ' . $order->getStatus()->label() . "\n";
echo 'Total: ' . $order->getTotalPrice() . "\n\n";

foreach ($order->getItems() as $index => $item) {
    echo ($index + 1) . '. ' . $item->describe() . "\n";

    $result = $cuisine->cook($item);
    foreach ($result->getLog() as $logLine) {
        echo '   - ' . $logLine . "\n";
    }

    echo '   Final state: ' . $result->getFinalState() . "\n\n";
}

$order->changeStatus(OrderStatus::Ready);

echo "\nOrder events:\n";
foreach ($order->getEvents() as $event) {
    echo '- [' . $event->getOccurredAt()->format('H:i:s') . '] ' . $event->getDescription() . "\n";
}
