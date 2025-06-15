<?php

namespace App\Application\Product;

use App\Domain\Entity\Product;
use App\Domain\Notifier\NotifierInterface;
use App\Domain\Repository\ProductRepositoryInterface;

class ProductUseCase
{
    public function __construct(
        protected ProductCookInterface $productCook,
        protected NotifierInterface $notifier,
        protected ProductHandler $handler,
        protected ProductRepositoryInterface $repository
    ) {
    }

    public function run(): Product {
        $product = $this->productCook->cook();
        $this->handler->handle($product);
        $this->repository->save($product);
        $this->notifier->notify();
        return $product;
    }
}