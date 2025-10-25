<?php

declare(strict_types=1);

namespace DataMapper;


class IdentityMap
{
    /**
     * @var array<string, array<int, object>>
     */
    private array $objects = [];

    
    public function get(string $className, int $id): ?object
    {
        return $this->objects[$className][$id] ?? null;
    }

    
    public function set(string $className, int $id, object $object): void
    {
        $this->objects[$className][$id] = $object;
    }

   
    public function has(string $className, int $id): bool
    {
        return isset($this->objects[$className][$id]);
    }

    
    public function clear(): void
    {
        $this->objects = [];
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        $stats = [];
        foreach ($this->objects as $className => $objects) {
            $stats[$className] = count($objects);
        }
        return $stats;
    }
} 