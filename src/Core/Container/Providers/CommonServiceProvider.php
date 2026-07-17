<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\CommonInitializer;
use App\Core\Container\Initialization\Initializers\InitializerInterface;
use App\Core\Container\Initialization\Payload\CommonPayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;

class CommonServiceProvider extends AbstractInitializableServiceProvider
{
    /**
     * @param CommonPayload $payload
     * @note non-null guarantee is enforced by parent class
     */
    protected function doRegisterServices(Container $container, ?PayloadInterface $payload): void
    {
        $container->singleton(DotEnvConfigInterface::class, $payload->dotEnvConfig);
    }

    protected function createInitializer(Container $container): InitializerInterface
    {
        return new CommonInitializer($container->get(PathResolverInterface::class));
    }
}
