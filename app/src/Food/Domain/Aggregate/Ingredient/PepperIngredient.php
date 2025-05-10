<?php

declare(strict_types=1);

namespace App\Food\Domain\Aggregate\Ingredient;

use App\Food\Domain\Aggregate\FoodIngredient;
use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodMass;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class PepperIngredient extends FoodIngredient
{
    public function __construct()
    {
        parent::__construct(new FoodTitle('pepper'), new FoodMass(25), new FoodCalorie(0));
    }
}
