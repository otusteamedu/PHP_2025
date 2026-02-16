<?php

namespace App\Application\Decorator;

use App\Domain\Entity\Interface\ProductInterface;

class RecipeDecorator extends ProductDecorator
{
    private array $recipe;

    public function __construct(ProductInterface $product, array $recipe)
    {
        parent::__construct($product);
        $this->recipe = $recipe;
    }

    protected function additionalIngredients(): array
    {
        return $this->recipe;
    }
}
