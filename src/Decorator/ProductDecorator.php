<?php

namespace App\Decorator;

use App\Model\Product;

abstract class ProductDecorator implements Product
{
    protected Product $product;
    protected string $ingredientLabel;
    protected float $priceDelta;
    protected string $nameSuffix;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getName(): string
    {
        return $this->product->getName() . ' ' . $this->nameSuffix;
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + $this->priceDelta;
    }

    public function getIngredients(): array
    {
        return array_merge($this->product->getIngredients(), [$this->ingredientLabel]);
    }
}


