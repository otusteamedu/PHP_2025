<?php

declare(strict_types=1);

namespace Otus\DataMapper\Application\Persistence\Repository;

use DateMalformedStringException;
use Otus\DataMapper\Application\Persistence\Mapper\ProductMapper;
use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;

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
     * @param int $id
     *
     * @return Product|null
     *
     * @throws DateMalformedStringException
     */
    public function findById(int $id): ?Product
    {
        return $this->mapper->findById($id);
    }

    /**
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     *
     * @return iterable
     *
     * @throws DateMalformedStringException
     */
    public function findAll(array $criteria = [], int $limit = 100, int $offset = 0): iterable
    {
        return $this->mapper->findAll($criteria, $limit, $offset);
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function save(Product $product): bool
    {
        if ($product->id === null) {
            return $this->mapper->insert($product);
        }

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
