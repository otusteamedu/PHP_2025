<?php

namespace App\Domain\Order;

use App\Domain\Ingredient\IngredientInterface;

readonly class OrderItemModel implements OrderItemModelInterface
{
    public function __construct(
        private string $productName,
        private string $recipeType,
        private int    $count,
        /** @var IngredientInterface[] */
        private array  $additionalIngredients = []
    ) {}

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getRecipeType(): string
    {
        return $this->recipeType;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getAdditionalIngredients(): array
    {
        return $this->additionalIngredients;
    }
}