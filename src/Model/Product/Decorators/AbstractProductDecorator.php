<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

use Dinargab\Homework15\Model\Product\ProductInterface;

class AbstractProductDecorator implements ProductInterface
{
    protected const INGREDIENT_NAME = "ingredient";
    public function __construct(protected ProductInterface $product)
    {

    }

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getPrice(): int
    {
        return $this->product->getPrice();
    }

    public function getDescription(): string
    {
        return $this->product->getDescription();
    }

    public function getIngredients(): array
    {
        return $this->product->getIngredients();
    }
}