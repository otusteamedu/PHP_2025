<?php

namespace App\Core\Container\Config;

use App\Core\Utils\PathResolverInterface;

class ApplicationServicesConfigLoader implements ContainerConfigLoaderInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
        private readonly string $configFileName = 'services.php',
    ) {
    }

    public function load(): array
    {
        $configPathDir = $this->pathResolver->getConfigPath();
        $configFile = "$configPathDir/$this->configFileName";

        if (!is_file($configFile)) {
            throw new \RuntimeException("Configuration file not found: $configFile");
        }

        $config = require $configFile;
        $this->validateConfigStructure($config);

        return $config;
    }

    private function validateConfigStructure(array $config): void
    {
        foreach ($config as $domainName => $services) {
            if (!is_array($services)) {
                throw new \InvalidArgumentException("Domain '$domainName' must contain array of services.");
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
