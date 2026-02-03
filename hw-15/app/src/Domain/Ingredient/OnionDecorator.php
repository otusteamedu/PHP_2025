<?php

declare(strict_types=1);

namespace App\Domain\Ingredient;

use App\Domain\Product\ProductInterface;

final readonly class OnionDecorator implements ProductInterface
{
    public function __construct(
        private ProductInterface $product,
    ) {
    }

    public function getName(): string
    {
        return $this->product->getName() . ' + лук';
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + 0.30;
    }

    public function getBaseProduct(): ProductInterface
    {
        return $this->product->getBaseProduct();
    }
}
