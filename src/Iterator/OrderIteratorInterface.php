<?php

declare(strict_types=1);

namespace Restaurant\Iterator;

interface OrderIteratorInterface
{
    public function hasNext(): bool;

    public function next(): OrderStatus;

    public function current(): OrderStatus;

    public function reset(): void;
}
