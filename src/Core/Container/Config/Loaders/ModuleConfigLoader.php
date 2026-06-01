<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Data\ConfigInterface;
use App\Core\Container\Config\Data\Module\ModuleConfig;
use App\Core\Container\Config\Data\Module\ModuleConfigAggregator;
use App\Core\Container\Config\Data\Module\ServiceConfig;
use App\Core\Utils\PathResolverInterface;

class ModuleConfigLoader implements ConfigLoaderInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function load(): ConfigInterface
    {
        $config = require $this->pathResolver->getConfigPath() . '/services.php';

        $this->validateConfigStructure($config);

        $modules = [];
        foreach ($config as $moduleName => $moduleServices) {
            $services = [];
            foreach ($moduleServices as $serviceClass => $serviceConfig) {
                $services[] = new ServiceConfig(
                    definition: $serviceClass,
                    singleton: $serviceConfig['singleton'],
                    factory: $serviceConfig['factory'],
                );
            }
            $modules[] = new ModuleConfig(name: $moduleName, services: $services);
        }

        return new ModuleConfigAggregator($modules);
    }

    private function validateConfigStructure(array $config): void
    {
        foreach ($config as $moduleName => $services) {
            if (!is_array($services)) {
                throw new \InvalidArgumentException("Module '$moduleName' must contain array of services.");
            }

            foreach ($services as $serviceClass => $serviceConfig) {
                if (!isset($serviceConfig['singleton']) || !isset($serviceConfig['factory'])) {
                    throw new \InvalidArgumentException(
                        "Service '$serviceClass' must have 'singleton' and 'factory' keys."
                    );
                }
            }
        }
    }
}
