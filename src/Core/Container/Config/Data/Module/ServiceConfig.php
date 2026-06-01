<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\Module;

readonly class ServiceConfig
{
    public function __construct(
        private string $definition,
        private bool $singleton,
        private \Closure $factory,
    ) {
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    public function getFactory(): callable
    {
        return $this->factory;
    }
}
