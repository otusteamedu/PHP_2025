<?php declare(strict_types=1);

namespace Fastfood\DI;

use Exception;

class Container
{
    private array $definitions = [];
    private array $instances = [];

    public function set(string $id, callable $factory): void
    {
        $this->definitions[$id] = $factory;
    }

    /**
     * @throws Exception
     */
    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->definitions[$id])) {
            $this->instances[$id] = $this->definitions[$id]($this);

            // Проверяем, что фабрика не вернула null
            if ($this->instances[$id] === null) {
                unset($this->instances[$id]);
                throw new Exception("Service {$id} returned null");
            }

            return $this->instances[$id];
        }

        throw new Exception("Service {$id} not found");
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->instances[$id]);
    }

    public function remove(string $id): void
    {
        unset($this->definitions[$id]);
        unset($this->instances[$id]);
    }
}