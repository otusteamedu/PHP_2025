<?php

declare(strict_types=1);

namespace App\Decorators;

use App\Products\ProductInterface;

abstract class AbstractProductDecorator implements ProductDecoratorInterface
{
    public function __construct(
        protected ProductInterface $product
    ) {
    }

    public string $name {
        get {
            return $this->product->name;
        }
    }

    public int $price {
        get {
            return $this->product->price;
        }
    }

    public string $description {
        get {
            return $this->product->description;
        }
    }

    public function getDecoratedProduct(): ProductInterface
    {
        return $this->product;
    }
}
