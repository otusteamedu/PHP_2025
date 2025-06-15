<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{

    public function findAll(): array {
        // TODO: Implement findAll() method.
    }

    public function findById(int $id): Product {
        // TODO: Implement findById() method.
    }

    public function save(Product $product): void {
        // TODO: Implement save() method.
    }

    public function delete(Product $product): void {
        // TODO: Implement delete() method.
    }
}