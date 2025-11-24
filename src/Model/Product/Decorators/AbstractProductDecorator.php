<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

use Dinargab\Homework15\Model\Product\Decorators\Helpers\ProductDecoratorHelper;
use Dinargab\Homework15\Model\Product\ProductInterface;

abstract class AbstractProductDecorator implements ProductInterface
{
    public function __construct(protected ProductInterface $product)
    {

    }

    public function getName(): string
    {
        return ProductDecoratorHelper::addIngredientToName($this->product->getName(), $this->getIngredientName());
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
        $ingredients = $this->product->getIngredients();
        $ingredients[] = $this->getIngredientName();
        return $ingredients;
    }

    abstract function getIngredientName(): string;

}