<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Persistence\Repository;

use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;
use Otus\DataMapper\Infrastructure\Persistence\Mapper\ProductMapper;

final readonly class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @param ProductMapper $mapper
     */
    public function __construct(
        private ProductMapper $mapper
    ) {
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Product
    {
        return $this->mapper->findById($id);
    }

    /**
     * @inheritDoc
     */
    public function findAll(array $criteria = [], int $limit = 100, int $offset = 0): iterable
    {
        return $this->mapper->findAll($criteria, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function save(Product $product): bool
    {
        if ($product->id === null) {
            return $this->mapper->insert($product);
        }

        return $this->mapper->update($product);
    }

    /**
     * @inheritDoc
     */
    public function delete(Product $product): bool
    {
        return $this->mapper->delete($product);
    }
}
