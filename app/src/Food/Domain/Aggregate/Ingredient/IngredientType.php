<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Ingredient;

enum IngredientType: string
{
    case ONION = 'onion';
    case PEPPER = 'pepper';
    case SALAD = 'salad';
}