<?php

declare(strict_types=1);

namespace App\Core\Console\Metadata;

readonly class CommandMetadata
{
    public function __construct(
        private string $className,
        private string $commandName,
        private array $dependencyNames = [],
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function getDependencyNames(): array
    {
        return $this->dependencyNames;
    }
}
