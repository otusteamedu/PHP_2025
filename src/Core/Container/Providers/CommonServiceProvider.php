<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\CommonInitializer;
use App\Core\Container\Initialization\Payload\CommonPayload;
use App\Core\Utils\PathResolverInterface;

class CommonServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        /** @var CommonPayload $payload */
        $payload = $this->getInitializer($container)->initialize();

        $container->singleton(DotEnvConfigInterface::class, $payload->dotEnvConfig);
    }

    private function getInitializer(Container $container): CommonInitializer
    {
        return new CommonInitializer($container->get(PathResolverInterface::class));
    }
}
