<?php

declare(strict_types=1);

namespace Restaurant\Builder;

use Restaurant\Product\ProductInterface;
use Restaurant\Product\Burger;

class BurgerBuilder implements BuilderInterface
{
    private ?ProductInterface $product = null;

    public function reset(): void
    {
        $this->product = null;
    }

    public function createBurger(): ProductInterface
    {
        $this->product = new Burger();
        return $this->product;
    }

    public function getProduct(): ProductInterface
    {
        $product = $this->product;
        $this->reset();
        return $product;
    }
}
