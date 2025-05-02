<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Burger;

use App\Food\Domain\Aggregate\FoodIngredient;
use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodMass;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class BurgerBunIngredient extends FoodIngredient
{
    public function __construct()
    {
        parent::__construct(new FoodTitle('bun'), new FoodMass(100), new FoodCalorie(100));
    }

}