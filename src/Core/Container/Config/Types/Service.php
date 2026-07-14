<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\ConfigInterface;

readonly class Service implements ConfigInterface
{
    public function __construct(
        private string $definition,
        private bool $singleton,
        private \Closure $factory,
    ) {
    }

    public function getType(): ConfigType
    {
        return ConfigType::SERVICE;
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
