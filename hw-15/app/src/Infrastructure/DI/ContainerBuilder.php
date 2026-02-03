<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\Order\CreateOrder;
use App\Domain\Cooking\Factory\CookerFactory;
use App\Domain\EventDispatcher;
use App\Domain\Ingredient\Factory\IngredientDecoratorFactory;
use App\Domain\Order\OrderStatusChangedEvent;
use App\Domain\Order\Pipeline\OrderPipeline;
use App\Domain\Product\Factory\ProductFactory;
use App\Infrastructure\Notifier\PushNotifier;
use App\Infrastructure\Notifier\SmsNotifier;
use App\UserInterface\CreateOrderCommand;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(CookerFactory::class, function () {
            return new CookerFactory();
        });

        $container->singleton(OrderPipeline::class, function (Container $container) {
            return new OrderPipeline(
                $container->get(CookerFactory::class),
                $container->get(EventDispatcher::class)
            );
        });

        $container->singleton(SmsNotifier::class, function () {
            return new SmsNotifier();
        });

        $container->singleton(PushNotifier::class, function () {
            return new PushNotifier();
        });

        $container->singleton(EventDispatcher::class, function (Container $container) {
            $dispatcher = new EventDispatcher();

            $dispatcher->addListener(
                OrderStatusChangedEvent::class,
                $container->get(SmsNotifier::class)
            );

            $dispatcher->addListener(
                OrderStatusChangedEvent::class,
                $container->get(PushNotifier::class)
            );

            return $dispatcher;
        });

        $container->set(CreateOrderCommand::class, function (Container $container) {
            return new CreateOrderCommand(
                $container->get(CreateOrder::class)
            );
        });

        $container->singleton(ProductFactory::class, function () {
            return new ProductFactory();
        });

        $container->singleton(IngredientDecoratorFactory::class, function () {
            return new IngredientDecoratorFactory();
        });

        $container->set(CreateOrder::class, function (Container $container) {
            return new CreateOrder(
                $container->get(ProductFactory::class),
                $container->get(IngredientDecoratorFactory::class),
                $container->get(OrderPipeline::class),
            );
        });

        return $container;
    }
}
