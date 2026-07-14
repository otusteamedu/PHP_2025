<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Iterators;

use App\Core\Container\Config\Types\Module;
use App\Core\Container\Config\Types\ModuleAggregator;

class ModuleIterator implements \Iterator
{
    private int $position = 0;

    public function __construct(
        private readonly ModuleAggregator $aggregator,
    ) {
    }

    public function current(): Module
    {
        $module = $this->getCurrentModule();
        if ($module === null) {
            throw new \OutOfBoundsException('Iterator is out of bounds.');
        }

        return $module;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        $modules = $this->aggregator->getModules();

        return isset($modules[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    private function getCurrentModule(): ?Module
    {
        $modules = $this->aggregator->getModules();

        return $modules[$this->position] ?? null;
    }
}
