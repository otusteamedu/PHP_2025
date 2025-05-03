<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\AddBurger;

use App\Food\Domain\Aggregate\Ingredient\IngredientType;

class AddBurgerRequest
{
    public array $ingredients = [];

    public function __construct(public string $orderId, public ?string $title = null, string ...$ingredients)
    {
        foreach ($ingredients as $ingredient) {
            $this->ingredients[] = IngredientType::from($ingredient);
        }
        $this->ingredients = $ingredients;
    }

}