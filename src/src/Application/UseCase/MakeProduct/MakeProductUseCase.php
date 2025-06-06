<?php

namespace App\Application\UseCase\MakeProduct;

use App\Domain\Decorator\Product\ProductDecoratorInterface;

class MakeProductUseCase
{
    public function __construct(
        private $product,
    )
    {
    }

    public function __invoke()
    {
        return $this->product->makeProduct("Продукт"); 
    }
}