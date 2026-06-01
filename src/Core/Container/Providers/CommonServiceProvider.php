<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;
use App\Core\Container\Config\Loaders\ConfigLoaderFactory;
use App\Core\Container\Config\Loaders\ConfigLoaderType;
use App\Core\Container\Container;
use App\Core\Container\Context\AppContext;
use App\Core\Utils\PathResolver;
use App\Core\Utils\PathResolverInterface;

class CommonServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        $container->singleton(AppContext::class, static fn() => new AppContext());

        $container->singleton(PathResolverInterface::class, static fn() => new PathResolver());

        $container->singleton(ConfigLoaderFactory::class, static fn(Container $c) => new ConfigLoaderFactory($c));

        $container->singleton(
            DotEnvConfigInterface::class,
            static fn(Container $c) => $c
                ->get(ConfigLoaderFactory::class)
                ->create(ConfigLoaderType::DOT_ENV)
                ->load(),
        );
    }
}
