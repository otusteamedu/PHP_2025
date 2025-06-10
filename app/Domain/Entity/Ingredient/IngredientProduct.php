<?php
declare(strict_types=1);

namespace App\Domain\Entity\Ingredient;

use App\Domain\Entity\Products\ProductInterface;
use App\Domain\Patterns\Decorator\IngredientDecoratorInterface;

class IngredientProduct implements IngredientDecoratorInterface
{

    public function __construct(
        private readonly ProductInterface $product,
    )
    {
    }

    public function getTitle(): string
    {
        return $this->product->getName();
    }

    public function getPrice(): float
    {
        return $this->product->getPrice();
    }
}


