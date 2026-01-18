<?php

declare(strict_types=1);

namespace Otus\DataMapper\Collection;

use Iterator;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\ProductFactory;

final readonly class Lazy implements Iterator
{
    /**
     * @param Iterator $iterator
     */
    public function __construct(private Iterator $iterator)
    {
    }

    /**
     * @return Product
     */
    public function current(): Product
    {
        return ProductFactory::factory($this->iterator->current());
    }

    /**
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }
}
