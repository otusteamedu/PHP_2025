<?php

namespace Elisad5791\Phpapp;

use ReflectionClass;

class IdentityMap
{
    private array $entities = [];

    public function has(string $className, int $id): bool
    {
        return isset($this->entities[$className][$id]);
    }

    public function get(string $className, int $id): ?object
    {
        return $this->entities[$className][$id] ?? null;
    }

    public function set(object $entity): void
    {
        $className = get_class($entity);
        $id = $this->extractId($entity);
        $this->entities[$className][$id] = $entity;
    }

    public function remove(string $className, int $id): void
    {
        unset($this->entities[$className][$id]);
    }

    private function extractId(object $entity): int
    {
        $reflection = new ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        return $property->getValue($entity);
    }
}