<?php

declare(strict_types=1);

namespace App\Iterator;

use Iterator;

class PizzaStatusIterator implements Iterator
{
    private const STATUS_LIST = [
        'Заказ создан',
        'Готовится',
        'Готово',
    ];

    private int $position = 0;

    public function current(): string
    {
        return self::STATUS_LIST[$this->position];
    }

    public function hasNext(): bool
    {
        return isset(self::STATUS_LIST[$this->key() + 1]);
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset(self::STATUS_LIST[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
