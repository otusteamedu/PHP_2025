<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Domain\Product\Factory\ProductFactory;
use App\Domain\Product\Strategy\BurgerStrategy;
use App\Domain\Product\Strategy\SandwichStrategy;
use App\Domain\Product\Strategy\HotDogStrategy;

use App\Domain\Order\OrderBuilder;
use App\Domain\Interfaces\OrderBuilderInterface;

use App\Domain\Interfaces\OrderRepositoryInterface;
use App\Application\UseCases\CreateOrderUseCase;
use App\Application\UseCases\GetMenuUseCase;
use App\Application\UseCases\GetOrderUseCase;
use App\Application\UseCases\GetOrderStatusHistoryUseCase;
use App\Application\UseCases\CancelOrderUseCase;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Repositories\OrderRepository;
use PDO;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        self::registerInfrastructureServices($container);
        self::registerDomainServices($container);
        self::registerApplicationServices($container);

        return $container;
    }

    private static function registerInfrastructureServices(Container $container): void
    {
        $container->singleton(PDO::class, fn() => DatabaseConnection::getConnection());

        $container->singleton(OrderRepositoryInterface::class, function (Container $c) {
            return new OrderRepository(
                $c->get(PDO::class),
                $c->get(ProductFactory::class)
            );
        });
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
    }

    private static function registerApplicationServices(Container $container): void
    {
        $container->singleton(CreateOrderUseCase::class, function (Container $c) {
            return new CreateOrderUseCase(
                $c->get(ProductFactory::class),
                $c->get(OrderBuilderInterface::class),
                $c->get(OrderRepositoryInterface::class)
            );
        });

        $container->singleton(GetMenuUseCase::class, function (Container $c) {
            return new GetMenuUseCase(
                $c->get(ProductFactory::class)
            );
        });

        $container->singleton(GetOrderUseCase::class, function (Container $c) {
            return new GetOrderUseCase(
                $c->get(OrderRepositoryInterface::class)
            );
        });

        $container->singleton(GetOrderStatusHistoryUseCase::class, function (Container $c) {
            return new GetOrderStatusHistoryUseCase(
                $c->get(OrderRepositoryInterface::class)
            );
        });

        $container->singleton(CancelOrderUseCase::class, function (Container $c) {
            return new CancelOrderUseCase(
                $c->get(OrderRepositoryInterface::class)
            );
        });
    }
}
