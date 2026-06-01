<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Container;

class ConfigLoaderFactory
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function create(ConfigLoaderType $loaderType): ConfigLoaderInterface
    {
        $loaderClass = $loaderType->getLoaderClass();

        $reflection = new \ReflectionClass($loaderClass);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $loaderClass();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $dependencyName = $param->getType()?->getName();
            if ($dependencyName === null || !$this->container->has($dependencyName)) {
                throw new \RuntimeException(
                    "Cannot resolve dependency for parameter '{$param->getName()}' in '$loaderClass'"
                );
            }
            $dependencies[] = $this->container->get($dependencyName);
        }

        /** @var ConfigLoaderInterface $configLoader */
        $configLoader = $reflection->newInstanceArgs($dependencies);

        return $configLoader;
    }
}
