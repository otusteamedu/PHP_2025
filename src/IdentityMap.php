<?php

namespace Arlex2305k\PatternsDb;

class IdentityMap
{
    private array $objects = [];

    public function add(string $class, int $id, object $object): void
    {
        $key = $this->getKey($class, $id);
		if (isset($this->objects[$key])) {
			throw new \RuntimeException("Объект {$class}:{$id} уже существует");
		}
		$this->objects[$key] = $object;
    }

	public function update(string $class, int $id, object $object): void
	{
		$key = $this->getKey($class, $id);
		if (!isset($this->objects[$key])) {
			throw new \RuntimeException("Объект {$class}:{$id} не существует");
		}
		$this->objects[$key] = $object;
	}

	public function remove(string $class, int $id): void
	{
		$key = $this->getKey($class, $id);
		unset($this->objects[$key]);
	}

	public function clear(): void
	{
		$this->objects = [];
	}

	public function get(string $class, int $id): ?object
    {
        $key = $this->getKey($class, $id);
        return $this->objects[$key] ?? null;
    }

    public function exists(string $class, int $id): bool
    {
        $key = $this->getKey($class, $id);
        return isset($this->objects[$key]);
    }

    private function getKey(string $class, int $id): string
    {
        return $class . ':' . $id;
    }
}
