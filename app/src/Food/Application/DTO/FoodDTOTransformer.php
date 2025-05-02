<?php
declare(strict_types=1);


namespace App\Food\Application\DTO;

use App\Food\Domain\Aggregate\Food;

class FoodDTOTransformer
{
    public function __construct(private readonly FoodIngredientDTOTransformer $transformer)
    {
    }

    public function fromEntity(Food $food): FoodDTO
    {
        $dto = new FoodDTO();
        $dto->id = $food->getId();
        $dto->title = $food->getTitle()->getValue();
        foreach ($food->getIngredients() as $ingredient) {
            $dto->ingredients[] = $this->transformer->fromEntity($ingredient);
        }

        return $dto;
    }

    public function fromEntityList(array $foods): array
    {
        $foodDTOs = [];
        foreach ($foods as $food) {
            $foodDTOs[] = $this->fromEntity($food);
        }

        return $foodDTOs;
    }

}