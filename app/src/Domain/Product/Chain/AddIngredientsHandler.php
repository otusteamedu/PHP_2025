<?php

namespace App\Domain\Product\Chain;

class AddIngredientsHandler extends BaseCookingHandler
{

    protected function process(CookingContext $context): void
    {
       $ingredients = $this->getAllIngredients($context);

        foreach ($ingredients as $name => $count) {
            $context->product->addIngredient($name, $count);
        }
    }

    private function getAllIngredients(CookingContext $context): array
    {
        $result = $context->baseIngredients;

        foreach ($context->additionalIngredients as $name => $ingredientCount) {
            $context->storage->take($name, $ingredientCount);
            $result[$name] = isset($result[$name]) ? $result[$name] + $ingredientCount: $ingredientCount;
        }

        return $result;
    }
}