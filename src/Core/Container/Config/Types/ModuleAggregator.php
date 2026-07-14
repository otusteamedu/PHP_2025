<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Iterators\FlatServiceIterator;
use App\Core\Container\Config\Iterators\ModuleIterator;

readonly class ModuleAggregator implements ConfigInterface, \IteratorAggregate
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

    public function getIterator(): ModuleIterator
    {
        return new ModuleIterator($this);
    }

    public function getAllServicesIterator(): FlatServiceIterator
    {
        return new FlatServiceIterator($this);
    }
}
