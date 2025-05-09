<?php

declare(strict_types=1);

namespace App\Food\Domain\Aggregate\Ingredient;

use App\Food\Domain\Aggregate\FoodIngredient;
use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodMass;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class SaladIngredient extends FoodIngredient
{
    public function __construct()
    {
        parent::__construct(new FoodTitle('salad'), new FoodMass(10), new FoodCalorie(1));
    }
}
