<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\ConfigInterface;

readonly class Module implements ConfigInterface
{
    /**
     * @param Service[] $services
     */
    public function __construct(
        private string $name,
        private array $services,
    ) {
    }

    public function getType(): ConfigType
    {
        return ConfigType::MODULE;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }
}
