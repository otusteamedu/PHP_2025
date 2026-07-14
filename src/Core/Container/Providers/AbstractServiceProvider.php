<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    abstract protected function doRegisterServices(Container $container): void;

    final public function registerServices(Container $container): void
    {
        $this->doRegisterServices($container);
    }
}
