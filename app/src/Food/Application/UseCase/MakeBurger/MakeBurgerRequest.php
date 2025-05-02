<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

use App\Food\Domain\Aggregate\Ingredient\IngredientType;

class MakeBurgerRequest
{
    public array $ingredients = [];

    public function __construct(public ?string $title = null, string ...$ingredients)
    {
        foreach ($ingredients as $ingredient) {
            $this->ingredients[] = IngredientType::from($ingredient);
        }
        $this->ingredients = $ingredients;
    }

}