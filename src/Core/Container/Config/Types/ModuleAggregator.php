<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\ConfigInterface;

readonly class ModuleAggregator implements ConfigInterface
{
    public function __construct(
        private array $modules,
    ) {
    }

    public function getType(): ConfigType
    {
        return ConfigType::MODULE_AGGREGATOR;
    }

    /**
     * @return Module[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
