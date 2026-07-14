<?php

declare(strict_types=1);

namespace App\Core\Container\Builder;

use App\Core\Container\Config\ContainerConfig;
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

        $factory = $container->get(ServiceProviderFactory::class);
        foreach ($factory->createProviders() as $serviceProvider) {
            $serviceProvider->registerServices($container);
        }

        return $container;
    }

    private static function registerBaseInfrastructure(Container $container): void
    {
        $container->singleton(ContainerConfig::class, static fn() => new ContainerConfig());

        $container->set(
            ContextDetector::class,
            static fn(Container $c) => new ContextDetector(
                array_map(
                    static fn(string $class) => new $class(),
                    $c->get(ContainerConfig::class)->getContextDetectionStrategies(),
                ),
            ),
        );

        $container->singleton(
            ServiceProviderFactory::class,
            static fn(Container $c) => new ServiceProviderFactory(
                $c->get(ContextDetector::class),
                $c->get(ContainerConfig::class),
            ),
        );

        $container->singleton(PathResolverInterface::class, static fn() => new PathResolver());
    }
}
