<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Food\Domain\Aggregate\VO\FoodMass;

abstract class FoodIngredient
{
    public function __construct(
        protected readonly FoodTitle   $title,
        protected readonly FoodMass    $mass,
        protected readonly FoodCalorie $calorie,
    )
    {
    }

    public function getTitle(): FoodTitle
    {
        return $this->title;
    }

    public function getCalorie(): FoodCalorie
    {
        return $this->calorie;
    }

    public function getMass(): FoodMass
    {
        return $this->mass;
    }

}