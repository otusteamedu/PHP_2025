<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\ContainerConfigLoaderInterface;
use App\Core\Container\Container;

class ApplicationServiceProvider implements ServiceProviderInterface
{
    public function __construct(
        private readonly ContainerConfigLoaderInterface $configLoader,
    ) {
    }

    public function registerServices(Container $container): void
    {
        $config = $this->configLoader->load();

        foreach ($config as $domainName => $services) {
            foreach ($services as $serviceDefinition => $serviceConfig) {
                if ($serviceConfig['singleton'] === true) {
                    $container->singleton($serviceDefinition, $serviceConfig['factory']);
                } else {
                    $container->set($serviceDefinition, $serviceConfig['factory']);
                }
            }
        }
    }
}
