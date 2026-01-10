<?php

namespace BookstoreApp\Infrastructure\Database;

class IdentityMap
{
    private array $identityMap = [];

    public function get(string $className, int $id): ?object
    {
        $key = $this->getKey($className, $id);
        return $this->identityMap[$key] ?? null;
    }

    public function set(object $entity): void
    {
        $className = get_class($entity);
        $id = $entity->getId();
        $key = $this->getKey($className, $id);

        $this->identityMap[$key] = $entity;
    }

    public function has(string $className, int $id): bool
    {
        $key = $this->getKey($className, $id);
        return isset($this->identityMap[$key]);
    }

    public function remove(string $className, int $id): void
    {
        $key = $this->getKey($className, $id);
        unset($this->identityMap[$key]);
    }

    public function clear(): void
    {
        $this->identityMap = [];
    }

    private function getKey(string $className, int $id): string
    {
        return $className . '_' . $id;
    }
}