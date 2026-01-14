<?php

declare(strict_types=1);

namespace Otus\DataMapper\Repository;

use Otus\DataMapper\Collection\Eager;
use Otus\DataMapper\Collection\Lazy;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Mapper\ProductMapper;
use Otus\DataMapper\Sql\Command;

readonly class ProductRepository
{
    /**
     * @param ProductMapper $mapper
     */
    public function __construct(protected ProductMapper $mapper)
    {
    }

    /**
     * @param Command $command
     *
     * @return Lazy
     */
    public function lazy(Command $command): Lazy
    {
        return $this->mapper->lazy($command);
    }

    /**
     * @param Command $command
     *
     * @return Eager
     */
    public function eager(Command $command): Eager
    {
        return $this->mapper->eager($command);
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function insert(Product $product): bool
    {
        return $this->mapper->insert($product);
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function update(Product $product): bool
    {
        return $this->mapper->update($product);
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function delete(Product $product): bool
    {
        return $this->mapper->delete($product);
    }
}
