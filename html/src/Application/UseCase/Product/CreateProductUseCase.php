<?php

declare(strict_types=1);

namespace Otus\DataMapper\Application\UseCase\Product;

use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;

final readonly class CreateProductUseCase
{
    /**
     * @param ProductRepositoryInterface $repository
     */
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
    }

    /**
     * @param string $brand
     * @param string $title
     * @param int $price
     * @param int $capacity
     * @param bool $hidden
     *
     * @return Product
     */
    public function execute(
        string $brand,
        string $title,
        int $price,
        int $capacity,
        bool $hidden = false
    ): Product {
        $product = new Product(
            brand: $brand,
            title: $title,
            price: $price,
            capacity: $capacity,
            hidden: $hidden
        );

        $this->repository->save($product);

        return $product;
    }
}
