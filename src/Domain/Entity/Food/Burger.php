<?php

declare(strict_types=1);

namespace App\Domain\Entity\Food;

final class Burger extends FoodAbstract
{
    protected string $name = 'Burger';
    protected array $baseIngredients = ['Bun'];
    protected array $ingredientsRecipe = ['Cutlet', 'Lettuce', 'Tomato', 'Onion'];
}
