<?php declare(strict_types=1);

namespace App\Decorator;

use App\Core\FoodProductInterface;

abstract class IngredientDecorator implements FoodProductInterface
{
    protected FoodProductInterface $product;

    public function __construct(
        FoodProductInterface $product
    ) {
        $this->product = $product;
    }

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getIngredients(): array
    {
        return $this->product->getIngredients();
    }

    public function getDescription(): string
    {
        return $this->getName() . ", состав: " . implode(", ", $this->getIngredients());
    }
}
