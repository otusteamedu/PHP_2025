<?php

declare(strict_types=1);

namespace Otus\DataMapper\Application\UseCase\Product;

use DateTime;
use Otus\DataMapper\Domain\Entity\Product;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;

final readonly class UpdateProductUseCase
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
     * @param array $data
     *
     * @return Product|null
     */
    public function execute(int $id, array $data): ?Product
    {
        $product = $this->repository->findById($id);

        if (!$product instanceof Product) {
            return null;
        }

        if (isset($data['brand'])) {
            $product->brand = $data['brand'];
        }

        if (isset($data['title'])) {
            $product->title = $data['title'];
        }

        if (isset($data['price'])) {
            $product->price = $data['price'];
        }

        if (isset($data['capacity'])) {
            $product->capacity = $data['capacity'];
        }

        if (isset($data['hidden'])) {
            $product->hidden = $data['hidden'];
        }

        $product->updatedAt = new DateTime();

        $this->repository->save($product);

        return $product;
    }
}
