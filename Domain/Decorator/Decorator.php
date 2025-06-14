<?php

declare(strict_types=1);

namespace Domain\Decorator;

use Domain\Products\Product;

abstract class Decorator implements Product
{
    public function __construct(protected readonly Product $product)
    {
    }

    public function getName(): ?string
    {
        return $this->product->getName();
    }

    public function getPrice(): ?float
    {
        return $this->product->getPrice();
    }
}


