<?php

class IdentityMap {
    private array $objects = [];

    public function add(string $key, object $object): void {
        $this->objects[$key] = $object;
    }

    public function get(string $key): ?object {
        return $this->objects[$key] ?? null;
    }
}
