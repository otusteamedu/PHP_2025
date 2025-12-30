<?php

declare(strict_types=1);

namespace Otus\DataMapper\Repository;

use Otus\DataMapper\Collection\Eager;
use Otus\DataMapper\Collection\Lazy;
use Otus\DataMapper\Condition\ConditionInterface;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Mapper\ProductMapper;

readonly class ProductRepository
{
    /**
     * @param ProductMapper $mapper
     */
    public function __construct(protected ProductMapper $mapper)
    {
    }

    /**
     * @param ConditionInterface $criteria
     *
     * @return Lazy
     */
    public function lazy(ConditionInterface $criteria): Lazy
    {
        return $this->mapper->lazy($criteria);
    }

    /**
     * @param ConditionInterface $criteria
     *
     * @return Eager
     */
    public function eager(ConditionInterface $criteria): Eager
    {
        return $this->mapper->eager($criteria);
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
