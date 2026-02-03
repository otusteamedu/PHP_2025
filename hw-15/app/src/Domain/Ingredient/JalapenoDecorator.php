<?php

declare(strict_types=1);

namespace App\Domain\Ingredient;

use App\Domain\Product\ProductInterface;

final readonly class JalapenoDecorator implements ProductInterface
{
    public function __construct(
        private ProductInterface $product,
    ) {
    }

    public function getName(): string
    {
        return $this->product->getName() . ' + халапеньо';
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + 0.70;
    }

    public function getBaseProduct(): ProductInterface
    {
        return $this->product->getBaseProduct();
    }
}
