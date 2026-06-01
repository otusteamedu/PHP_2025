<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\Module;

use App\Core\Container\Config\Data\ConfigInterface;

readonly class ModuleConfigAggregator implements ConfigInterface
{
    public function __construct(
        private array $modules,
    ) {
    }

    /**
     * @return ModuleConfig[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
