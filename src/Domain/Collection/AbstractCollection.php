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

    protected function addEntity(EntityInterface $entity): void
    {
        $this->items[] = $entity;
    }
}
