<?php

namespace App\Iterator;


use App\FastFoodItemInterface;
use ArrayIterator;
use IteratorAggregate;

class Order implements IteratorAggregate {
    private array $items = [];

    public function addItem(FastFoodItemInterface $item): void {
        $this->items[] = $item;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}