<?php

class IdentityMap
{
    private array $objects = [];

    public function has(int $id): bool
    {
        return isset($this->objects[$id]);
    }

    public function get(int $id): ?Hall
    {
        return $this->objects[$id] ?? null;
    }

    public function set(Hall $hall): void
    {
        $this->objects[$hall->getId()] = $hall;
    }
}
