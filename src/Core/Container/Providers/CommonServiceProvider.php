<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Config\ConfigInterface;
use App\Core\Config\DotEnvConfig;
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

        $container->singleton(
            ConfigInterface::class,
            static fn(Container $c) => new DotEnvConfig($c->get(PathResolverInterface::class)),
        );
    }
}
