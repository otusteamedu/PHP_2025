<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;
use App\Core\Http\View\View;
use App\Core\Utils\PathResolverInterface;

class UiServiceProvider extends AbstractServiceProvider
{
    protected function doRegisterServices(Container $container): void
    {
        $container->singleton(
            View::class,
            static fn(Container $c) => new View(
                $c->get(PathResolverInterface::class),
            )
        );
    }
}
