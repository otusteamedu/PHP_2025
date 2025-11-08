<?php

namespace App\Container;

class Container
{
    private array $bindings = [];

    public function set(string $id, callable $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new \RuntimeException("Сервис '$id' не найден в контейнере.");
        }

        return $this->bindings[$id]();
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }
}
