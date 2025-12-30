<?php

declare(strict_types=1);

namespace Otus\DataMapper\Collection;

use Iterator;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\ProductFactory;

final class Eager implements Iterator
{
    /**
     * @param array $list
     */
    public function __construct(private array $list)
    {
    }

    /**
     * @return Product
     */
    public function current(): Product
    {
        return ProductFactory::factory(current($this->list));
    }

    /**
     * @return mixed
     */
    public function key(): mixed
    {
        return key($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function rewind(): void
    {
        reset($this->list);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->list) !== null;
    }
}
