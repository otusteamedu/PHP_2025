<?php
declare(strict_types=1);

namespace Domain\Catalog\Products\Repository;

use Domain\Catalog\Products\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function findAll(): iterable;

    public function findById(int $id): ?Product;

    public function save(Product $product): void;

    public function delete(Product $product): void;
}