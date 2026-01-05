<?php

namespace App\Collections;

/**
 * Коллекция объектов для массового получения данных
 */
class Collection implements \IteratorAggregate, \Countable
{
    private array $items = [];
    
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    public function count(): int
    {
        return count($this->items);
    }
    
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}