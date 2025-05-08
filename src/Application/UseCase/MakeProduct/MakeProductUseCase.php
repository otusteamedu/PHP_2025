<?php

namespace Application\UseCase\MakeProduct;

use Domain\Factory\Product\ProductFactoryInterface;

class MakeProductUseCase
{
    public function __construct(
        private readonly ProductFactoryInterface $product,
    )
    {
    }

    public function __invoke()
    {
        return $this->product->makeProduct("Продукт"); 
    }
}