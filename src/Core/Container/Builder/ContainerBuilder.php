<?php

declare(strict_types=1);

namespace App\Core\Container\Builder;

use App\Core\Container\Container;
use App\Core\Container\Context\ContextDetector;
use App\Core\Container\Providers\ServiceProviderFactory;
use App\Core\Utils\PathResolver;
use App\Core\Utils\PathResolverInterface;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        self::registerBaseInfrastructure($container);

        $factory = new ServiceProviderFactory($container->get(ContextDetector::class));

        foreach ($factory->createProviders() as $serviceProvider) {
            $serviceProvider->registerServices($container);
        }

        return $container;
    }

    private static function registerBaseInfrastructure(Container $container): void
    {
        $container->set(ContextDetector::class, static fn() => new ContextDetector());
        $container->singleton(PathResolverInterface::class, static fn() => new PathResolver());
    }
}
