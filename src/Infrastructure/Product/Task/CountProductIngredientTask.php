<?php

namespace App\Infrastructure\Product\Task;

use App\Application\Task\Task;
use App\Domain\Entity\Product;

class CountProductIngredientTask extends Task
{
    public function run(Product $product): array {
        $ingredients = $product->getIngredients();

        $countedIngredients = [];

        foreach ($ingredients as $ingredient) {
            if (empty($countedIngredients[$ingredient])) {
                $countedIngredients[$ingredient] = 1;
            } else {
                $countedIngredients[$ingredient]++;
            }
        }

        return $countedIngredients;
    }
}