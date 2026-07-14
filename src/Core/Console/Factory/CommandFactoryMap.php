<?php

declare(strict_types=1);

namespace App\Core\Console\Factory;

use App\Core\Console\Metadata\CommandMetadata;
use App\Core\Container\Container;

class CommandFactoryMap
{
    /**
     * @param CommandMetadata[] $commandMetadata
     */
    public function __construct(
        private readonly Container $container,
        private readonly array $commandMetadata,
    ) {
    }

    public function getCommandsFactoryMap(): array
    {
        $mapping = [];
        foreach ($this->commandMetadata as $metadata) {
            $mapping[$metadata->getCommandName()] = $this->createCommandFactory($metadata);
        }

        return $mapping;
    }

    private function createCommandFactory(CommandMetadata $metadata): callable
    {
        return function () use ($metadata) {
            $className = $metadata->getClassName();
            $dependencies = [];

            foreach ($metadata->getDependencyNames() as $dependencyName) {
                $dependencies[] = $this->container->get($dependencyName);
            }

            return new $className(...$dependencies);
        };
    }
}
