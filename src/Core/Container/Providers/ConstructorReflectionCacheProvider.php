<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Resolver\CachedConstructorDependencyResolver;
use App\Core\Resolver\ConstructorDependencyResolver;
use App\Core\Resolver\ConstructorDependencyResolverInterface;
use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;

class ConstructorReflectionCacheProvider extends AbstractServiceProvider
{
    protected function doRegisterServices(Container $container): void
    {
        $this->registerBaseResolver($container);
        $this->configureResolverInterface($container);
    }

    private function registerBaseResolver(Container $container): void
    {
        $container->singleton(
            ConstructorDependencyResolver::class,
            static fn() => new ConstructorDependencyResolver(),
        );
    }

    private function configureResolverInterface(Container $container): void
    {
        $resolver = $this->createResolver($container);
        $container->singleton(ConstructorDependencyResolverInterface::class, $resolver);
    }

    private function createResolver(Container $container): ConstructorDependencyResolverInterface
    {
        $config = $container->get(DotEnvConfigInterface::class);
        $env = $config->get('APP_ENV', 'prod');
        $isDev = ($env === 'dev');

        $baseResolver = $container->get(ConstructorDependencyResolver::class);
        $cache = $container->get(DependencyCacheInterface::class);

        if ($isDev) {
            return $baseResolver;
        }

        return new CachedConstructorDependencyResolver($baseResolver, $cache);
    }
}
