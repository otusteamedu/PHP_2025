<?php

namespace App\Infrastructure\Recipe\Dodo;
use RuntimeException as RuntimeExceptionAlias;

class DodoRecipeProvider
{
    /**
     * @throws RuntimeExceptionAlias
     */
    public function getRecipe(string $productType, string $recipeType): array
    {
        return match ($productType) {
            'pizza' => $this->getPizzaRecipe($recipeType),
            default => throw new RuntimeExceptionAlias("Unsupported product")
        };
    }

    /**
     * @throws RuntimeExceptionAlias
     */
    private function getPizzaRecipe(string $type): array
    {
        return match ($type) {
            'classic' => [
                'pizza-crust' => 1,
                'ketchup' => 1,
                'cheese' => 2,
                'tomato' => 1,
            ],
            default => throw new RuntimeExceptionAlias("Unknown pizza type")
        };
    }
}