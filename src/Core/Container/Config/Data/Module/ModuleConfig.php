<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\Module;

readonly class ModuleConfig
{
    /**
     * @param ServiceConfig[] $services
     */
    public function __construct(
        private string $name,
        private array $services,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ServiceConfig[]
     */
    public function getServices(): array
    {
        return $this->services;
    }
}
