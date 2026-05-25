<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;

interface ServiceProviderInterface
{
    public function registerServices(Container $container): void;
}
