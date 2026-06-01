<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Data\Module\ModuleConfigAggregator;
use App\Core\Container\Config\Loaders\ConfigLoaderInterface;
use App\Core\Container\Container;

class ModuleServiceProvider implements ServiceProviderInterface
{
    public function __construct(
        private readonly ConfigLoaderInterface $configLoader,
    ) {
    }

    public function registerServices(Container $container): void
    {
        /** @var ModuleConfigAggregator $moduleConfigAggregator */
        $moduleConfigAggregator = $this->configLoader->load();

        foreach ($moduleConfigAggregator->getModules() as $moduleConfig) {
            foreach ($moduleConfig->getServices() as $serviceConfig) {
                if ($serviceConfig->isSingleton()) {
                    $container->singleton($serviceConfig->getDefinition(), $serviceConfig->getFactory());
                } else {
                    $container->set($serviceConfig->getDefinition(), $serviceConfig->getFactory());
                }
            }
        }
    }
}
