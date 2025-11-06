<?php

declare(strict_types=1);

namespace Restaurant\Builder;

use Restaurant\Product\ProductInterface;
use Restaurant\Product\Sandwich;

class SandwichBuilder implements BuilderInterface
{
    private ?ProductInterface $product = null;

    public function reset(): void
    {
        $this->product = null;
    }

    public function createSandwich(): ProductInterface
    {
        $this->product = new Sandwich();
        return $this->product;
    }

    public function getProduct(): ProductInterface
    {
        $product = $this->product;
        $this->reset();
        return $product;
    }
}
