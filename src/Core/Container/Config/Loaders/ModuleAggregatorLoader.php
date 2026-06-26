<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Types\Module;
use App\Core\Container\Config\Types\ModuleAggregator;
use App\Core\Container\Config\Types\Service;
use App\Core\Utils\PathResolverInterface;

class ModuleAggregatorLoader implements ConfigLoaderInterface
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
                $services[] = new Service(
                    definition: $serviceClass,
                    singleton: $serviceConfig['singleton'],
                    factory: $serviceConfig['factory'],
                );
            }
            $modules[] = new Module(name: $moduleName, services: $services);
        }

        return new ModuleAggregator($modules);
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
