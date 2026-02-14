<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Domain\Product\Factory\ProductFactory;
use App\Domain\Product\Strategy\BurgerStrategy;
use App\Domain\Product\Strategy\SandwichStrategy;
use App\Domain\Product\Strategy\HotDogStrategy;

use App\Domain\Order\OrderBuilder;
use App\Domain\Interfaces\OrderBuilderInterface;

use App\Domain\Order\OrderSubject;
use App\Domain\Interfaces\OrderSubjectInterface;
use App\Infrastructure\Notifications\PushNotificationObserver;
use App\Infrastructure\Notifications\SmsNotificationObserver;

use App\Domain\Cooking\Factory\CookingProcessFactory;

use App\Domain\Interfaces\CreateOrderUseCaseInterface;
use App\Domain\Interfaces\GetMenuUseCaseInterface;
use App\Application\UseCases\CreateOrderUseCase;
use App\Application\UseCases\GetMenuUseCase;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        self::registerDomainServices($container);
        self::registerApplicationServices($container);

        return $container;
    }

    private static function registerDomainServices(Container $container): void
    {
        $container->singleton(BurgerStrategy::class, fn() => new BurgerStrategy());
        $container->singleton(SandwichStrategy::class, fn() => new SandwichStrategy());
        $container->singleton(HotDogStrategy::class, fn() => new HotDogStrategy());

        $container->singleton(ProductFactory::class, function (Container $c) {
            return new ProductFactory([
                $c->get(BurgerStrategy::class),
                $c->get(SandwichStrategy::class),
                $c->get(HotDogStrategy::class),
            ]);
        });

        $container->singleton(OrderBuilderInterface::class, fn() => new OrderBuilder());

        $container->singleton(PushNotificationObserver::class, fn() => new PushNotificationObserver());
        $container->singleton(SmsNotificationObserver::class, fn() => new SmsNotificationObserver());
        $container->singleton(OrderSubjectInterface::class, fn() => new OrderSubject());

        $container->singleton(CookingProcessFactory::class, function (Container $c) {
            return new CookingProcessFactory(
                $c->get(OrderSubjectInterface::class)
            );
        });
    }

    private static function registerApplicationServices(Container $container): void
    {
        $container->singleton(CreateOrderUseCaseInterface::class, function (Container $c) {
            return new CreateOrderUseCase(
                $c->get(ProductFactory::class),
                $c->get(OrderBuilderInterface::class),
                $c->get(OrderSubjectInterface::class),
                $c->get(CookingProcessFactory::class),
                $c->get(PushNotificationObserver::class),
                $c->get(SmsNotificationObserver::class)
            );
        });

        $container->singleton(GetMenuUseCaseInterface::class, function (Container $c) {
            return new GetMenuUseCase(
                $c->get(ProductFactory::class)
            );
        });
    }
}
