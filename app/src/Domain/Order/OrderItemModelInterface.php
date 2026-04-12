<?php

namespace App\Domain\Order;

use App\Domain\Ingredient\IngredientInterface;

interface OrderItemModelInterface
{
    public function getProductName(): string;
    public function getRecipeType(): string;
    public function getCount(): int;
    /** @return IngredientInterface[] */
    public function getAdditionalIngredients(): array;
}