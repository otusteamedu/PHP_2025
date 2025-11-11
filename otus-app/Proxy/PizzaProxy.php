<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Adapter\Pizza;

class PizzaProxy extends Pizza
{
    private ?bool $isEnoughIngredients = null;

    public function checkIngredients(): bool
    {
        echo 'checking ';

        if ($this->isEnoughIngredients === null) {
            $isEnoughIngredients = true;
            $currentAvailableProductList = [
                'cheese',
                'sauce',
                'tomato',
                'dough',
            ];

            foreach ($this->getIngredientList() as $ingredient) {
                if ($isEnoughIngredients === false) {
                    continue;
                }

                if (!in_array($ingredient, $currentAvailableProductList, true)) {
                    $isEnoughIngredients = false;
                }
            }

            $this->isEnoughIngredients = $isEnoughIngredients;
        }

        return $this->isEnoughIngredients;
    }
}
