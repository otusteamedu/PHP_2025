<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Types\ModuleAggregator;
use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\ModuleInitializer;
use App\Core\Container\Initialization\Payload\ModulePayload;
use App\Core\Utils\PathResolverInterface;

class ModuleServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        /** @var ModulePayload $payload */
        $payload = $this->getInitializer($container)->initialize();
        $config = $payload->moduleAggregatorConfig;

        $this->registerModuleAggregatorConfig($container, $config);
        $this->registerModuleServices($container, $config);
    }

    private function getInitializer(Container $container): ModuleInitializer
    {
        return new ModuleInitializer($container->get(PathResolverInterface::class));
    }

    private function registerModuleAggregatorConfig(Container $container, ModuleAggregator $config): void
    {
        $container->singleton($config->getType()->value, $config);
    }

    private function registerModuleServices(Container $container, ModuleAggregator $config): void
    {
        foreach ($config->getModules() as $module) {
            foreach ($module->getServices() as $service) {
                if ($service->isSingleton()) {
                    $container->singleton($service->getDefinition(), $service->getFactory());
                } else {
                    $container->set($service->getDefinition(), $service->getFactory());
                }
            }
        }
    }
}
