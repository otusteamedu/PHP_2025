<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Iterators;

use App\Core\Container\Config\Types\Module;
use App\Core\Container\Config\Types\ModuleAggregator;
use App\Core\Container\Config\Types\Service;

class FlatServiceIterator implements \Iterator
{
    private int $modulePosition = 0;
    private int $servicePosition = 0;
    private int $flatKey = 0;

    public function __construct(
        private readonly ModuleAggregator $aggregator,
    ) {
    }

    public function current(): Service
    {
        $service = $this->getCurrentService();
        if ($service === null) {
            throw new \OutOfBoundsException('Iterator is out of bounds.');
        }

        return $service;
    }

    public function next(): void
    {
        $module = $this->getCurrentModule();
        if ($module === null) {
            return;
        }

        $servicesCount = count($module->getServices());
        if ($this->servicePosition + 1 < $servicesCount) {
            $this->servicePosition++;
        } else {
            $this->modulePosition++;
            $this->servicePosition = 0;
        }

        $this->flatKey++;
    }

    public function key(): int
    {
        return $this->flatKey;
    }

    public function valid(): bool
    {
        return $this->getCurrentService() !== null;
    }

    public function rewind(): void
    {
        $this->modulePosition = 0;
        $this->servicePosition = 0;
        $this->flatKey = 0;
    }

    private function getCurrentModule(): ?Module
    {
        $modules = $this->aggregator->getModules();

        return $modules[$this->modulePosition] ?? null;
    }

    private function getCurrentService(): ?Service
    {
        $module = $this->getCurrentModule();
        if ($module === null) {
            return null;
        }

        $services = $module->getServices();

        return $services[$this->servicePosition] ?? null;
    }
}
