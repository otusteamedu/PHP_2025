<?php

declare(strict_types=1);

namespace App\Domain\Entity\Food;

final class Sandwich extends FoodAbstract
{
    protected string $name = 'Sandwich';
    protected array $baseIngredients = ['Bread'];
    protected array $ingredientsRecipe = ['Ham', 'Lettuce'];
}
