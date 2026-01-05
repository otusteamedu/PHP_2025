<?php
declare(strict_types=1);

class IdentityMap
{
    /**
     * @var array<string, array<int, object>>
     */
    private array $entities = [];

    /**
     * @param string $className
     * @param int $id
     * @return object|null
     */
    public function get(string $className, int $id): ?object
    {
        return $this->entities[$className][$id] ?? null;
    }

    /**
     * @param string $className
     * @param int $id
     * @param object $entity
     * @return void
     */
    public function set(string $className, int $id, object $entity): void
    {
        $this->entities[$className][$id] = $entity;
    }

    /**
     * @param string $className
     * @param int $id
     * @return bool
     */
    public function has(string $className, int $id): bool
    {
        return isset($this->entities[$className][$id]);
    }

    /**
     * @param string $className
     * @param int $id
     * @return void
     */
    public function remove(string $className, int $id): void
    {
        unset($this->entities[$className][$id]);
    }
}
