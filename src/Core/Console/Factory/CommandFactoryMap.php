<?php

declare(strict_types=1);

namespace App\Core\Console\Factory;

use App\Core\Console\Metadata\CommandMetadata;
use App\Core\Console\Metadata\CommandMetadataExtractor;
use App\Core\Container\Container;

class CommandFactoryMap
{
    private readonly CommandMetadataExtractor $extractor;

    public function __construct(
        private readonly Container $container,
    ) {
        $this->extractor = $this->container->get(CommandMetadataExtractor::class);
    }

    public function getCommandsFactoryMap(): array
    {
        $commandsMetadata = $this->extractor->extractCommandsMetadata();
        $map = [];

        foreach ($commandsMetadata as $metadata) {
            $map[$metadata->getCommandName()] = $this->createCommandFactory($metadata);
        }

        return $map;
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
