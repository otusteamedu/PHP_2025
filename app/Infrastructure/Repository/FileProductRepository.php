<?php
declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Catalog\Products\Entity\Product;

class FileProductRepository
{
    public function findAll(): iterable
    {
        // TODO: Implement findAll() method.
        return [];
    }

    public function findById(int $id): ?Product
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(Product $product): void
    {
        // Имитация сохранения в БД с присваиванием ID
        $reflectionProperty = new \ReflectionProperty(Product::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($product, 1);
    }

    public function delete(Product $product): void
    {
        // TODO: Implement delete() method.
    }
}