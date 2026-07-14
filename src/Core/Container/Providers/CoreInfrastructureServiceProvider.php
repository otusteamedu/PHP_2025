<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\Connection\PDOWrapper;
use App\Core\Database\Factory\CollectionFactory;
use App\Core\Storage\KeyValue\Driver\MemcachedDriver;
use App\Core\Storage\KeyValue\Driver\RedisDriver;

class CoreInfrastructureServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        $this->registerDatabaseServices($container);
        $this->registerKeyValueServices($container);
    }

    private function registerDatabaseServices(Container $container): void
    {
        $container->singleton(
            PDOWrapper::class,
            static fn(Container $c) => new PDOWrapper($c->get(DotEnvConfigInterface::class)),
        );

        $container->singleton(
            DatabaseQueryExecutor::class,
            static fn(Container $c) => new DatabaseQueryExecutor($c->get(PDOWrapper::class)),
        );

        $container->singleton(CollectionFactory::class, static fn() => new CollectionFactory());
    }

    private function registerKeyValueServices(Container $container): void
    {
        $container->singleton(
            RedisDriver::class,
            static fn(Container $c) => new RedisDriver($c->get(DotEnvConfigInterface::class)),
        );

        $container->singleton(
            MemcachedDriver::class,
            static fn(Container $c) => new MemcachedDriver($c->get(DotEnvConfigInterface::class)),
        );
    }
}
