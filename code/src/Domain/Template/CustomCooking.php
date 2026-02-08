<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Template;

class CustomCooking extends CookingProcess
{
    protected function addIngredients(string $baseProduct, array $customIngredients): string
    {
        $ingredients = implode(', ', $customIngredients);
        return $baseProduct . ' с вашими ингредиентами: ' . $ingredients;
    }
}
