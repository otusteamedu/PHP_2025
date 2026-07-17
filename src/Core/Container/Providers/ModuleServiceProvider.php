<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\Types\ModuleAggregator;
use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\InitializerInterface;
use App\Core\Container\Initialization\Initializers\ModuleInitializer;
use App\Core\Container\Initialization\Payload\ModulePayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;

class ModuleServiceProvider extends AbstractInitializableServiceProvider
{
    /**
     * @param ModulePayload $payload
     * @note non-null guarantee is enforced by parent class
     */
    protected function doRegisterServices(Container $container, ?PayloadInterface $payload): void
    {
        $aggregator = $payload->moduleAggregatorConfig;

        $this->registerModuleAggregatorConfig($container, $aggregator);
        $this->registerModuleServices($container, $aggregator);
    }

    protected function createInitializer(Container $container): InitializerInterface
    {
        return new ModuleInitializer($container->get(PathResolverInterface::class));
    }

    private function registerModuleAggregatorConfig(Container $container, ModuleAggregator $aggregator): void
    {
        $container->singleton($aggregator->getType()->value, $aggregator);
    }

    private function registerModuleServices(Container $container, ModuleAggregator $aggregator): void
    {
        foreach ($aggregator->getAllServicesIterator() as $service) {
            if ($service->isSingleton()) {
                $container->singleton($service->getDefinition(), $service->getFactory());
            } else {
                $container->set($service->getDefinition(), $service->getFactory());
            }
        }
    }
}
