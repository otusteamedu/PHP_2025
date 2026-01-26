<?php

declare(strict_types=1);

namespace Otus\DataMapper\Application\UseCase\Product;

use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;

final readonly class DeleteProductUseCase
{
    /**
     * @param ProductRepositoryInterface $repository
     */
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function execute(int $id): bool
    {
        $product = $this->repository->findById($id);

        if (!$product instanceof Product) {
            return false;
        }

        return $this->repository->delete($product);
    }
}
