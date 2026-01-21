<?php

declare(strict_types=1);

namespace Otus\DataMapper\Domain\Repository;

use Otus\DataMapper\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     *
     * @return iterable<Product>
     */
    public function findAll(array $criteria = [], int $limit = 100, int $offset = 0): iterable;

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function save(Product $product): bool;

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function delete(Product $product): bool;
}
