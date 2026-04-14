<?php

namespace App\Domain\Collection;

use App\Domain\Entity\EntityInterface;

abstract class AbstractCollection implements \IteratorAggregate, \Countable
{
    private array $items = [];

    abstract public function add(EntityInterface $entity): void;

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function max(string $field): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $getter = 'get' . ucfirst($field);
        $hasGetter = array_all($this->items, static fn(EntityInterface $entity, $k) => method_exists($entity, $getter));
        if (!$hasGetter) {
            return null;
        }

        $max = null;
        foreach ($this->items as $entity) {
            if ($max === null) {
                $max = $entity->$getter();
                continue;
            }
            $max = max($entity->$getter(), $max);
        }

        return $max;
    }

    protected function addEntity(EntityInterface $entity): void
    {
        $this->items[] = $entity;
    }
}
