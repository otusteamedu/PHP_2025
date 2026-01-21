<?php

declare(strict_types=1);

namespace Otus\DataMapper\Application\UseCase\Product;

use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;

final readonly class GetProductsUseCase
{
    /**
     * @param ProductRepositoryInterface $repository
     */
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    /**
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     *
     * @return iterable<Product>
     */
    public function execute(array $criteria = [], int $limit = 100, int $offset = 0): iterable
    {
        return $this->repository->findAll($criteria, $limit, $offset);
    }
}
