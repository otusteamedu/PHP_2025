<?php

declare(strict_types=1);

namespace App\Adapter;

use App\ProductInterface;

class PizzaAdapter implements ProductInterface
{
    public function __construct(
        private PizzaInterface $pizza,
    ) {
    }

    public function getCustomDescription(): string
    {
        $dangerousIngredientList = [
            'cheese',
            'chile',
        ];
        $descriptionIngredientList = [];

        $description = $this->pizza->getDescription();

        foreach ($this->pizza->getIngredientList() as $ingredient) {
            if (in_array($ingredient, $dangerousIngredientList, true)) {
                $descriptionIngredientList['careful'][] = $ingredient;
            }
        }

        foreach ($descriptionIngredientList as $type => $ingredientList) {
            if ($type === 'careful') {
                $description .= 'Be careful, the pizza contains ' . implode(', ', $ingredientList);
            }
        }

        $this->pizza->setDescription($description);

        return $description;
    }
}
