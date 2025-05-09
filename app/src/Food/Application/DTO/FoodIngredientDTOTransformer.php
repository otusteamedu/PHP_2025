<?php

declare(strict_types=1);

namespace App\Food\Application\DTO;

use App\Food\Domain\Aggregate\FoodIngredient;

class FoodIngredientDTOTransformer
{
    public function fromEntity(FoodIngredient $ingredient): FoodIngredientDTO
    {
        $dto = new FoodIngredientDTO();
        $dto->title = $ingredient->getTitle()->getValue();
        $dto->mass = $ingredient->getMass()->getValue();
        $dto->calorie = $ingredient->getCalorie()->getValue();

        return $dto;
    }

    public function fromEntityList(array $ingredients): array
    {
        $ingredientDTOs = [];
        foreach ($ingredients as $ingredient) {
            $ingredientDTOs[] = $this->fromEntity($ingredient);
        }

        return $ingredientDTOs;
    }
}
