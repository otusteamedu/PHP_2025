<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): Product;

    public function save(Product $product): void;

    public function delete(Product $product): void;
}