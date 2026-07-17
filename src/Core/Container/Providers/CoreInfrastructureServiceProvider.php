<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\Connection\PDOWrapper;
use App\Core\Database\Factory\CollectionFactory;
use App\Core\Storage\KeyValue\Memcached\MemcachedDependencyCacheAdapter;
use App\Core\Storage\KeyValue\Memcached\MemcachedDriver;
use App\Core\Storage\KeyValue\Redis\RedisDependencyCacheAdapter;
use App\Core\Storage\KeyValue\Redis\RedisDriver;
use App\Core\Storage\KeyValue\Shared\CacheStorageType;
use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;

class CoreInfrastructureServiceProvider extends AbstractServiceProvider
{
    protected function doRegisterServices(Container $container): void
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

        $this->registerDependencyCacheAdapter($container);
    }

    private function registerDependencyCacheAdapter(Container $container): void
    {
        $container->singleton(
            DependencyCacheInterface::class,
            static function(Container $c) {
                $config = $c->get(DotEnvConfigInterface::class);

                $cacheStorageType = $config->has('CACHE_STORAGE')
                    ? CacheStorageType::tryFrom($config->get('CACHE_STORAGE'))
                    : CacheStorageType::Redis;

                switch ($cacheStorageType) {
                    case CacheStorageType::Memcached:
                        $driver = $c->get(MemcachedDriver::class);
                        return new MemcachedDependencyCacheAdapter($driver->getHandler());
                    default:
                        $driver = $c->get(RedisDriver::class);
                        return new RedisDependencyCacheAdapter($driver->getHandler());
                }
            },
        );
    }
}
