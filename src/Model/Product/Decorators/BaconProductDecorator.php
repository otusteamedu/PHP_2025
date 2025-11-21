<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

use Dinargab\Homework15\Model\Product\Decorators\Helpers\ProductDecoratorHelper;

class BaconProductDecorator extends AbstractProductDecorator
{

    protected const INGREDIENT_NAME = "bacon";

    public function getName(): string
    {
        return ProductDecoratorHelper::addIngredientToName($this->product->getName(), self::INGREDIENT_NAME);
    }

    public function getPrice(): int
    {
        return $this->product->getPrice() + 3;
    }

    public function getIngredients(): array
    {
        $ingredients = $this->product->getIngredients();
        $ingredients[] = self::INGREDIENT_NAME;
        return $ingredients;
    }
}