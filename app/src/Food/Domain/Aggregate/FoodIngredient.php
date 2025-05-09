<?php
declare(strict_types=1);

namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Food\Domain\Aggregate\VO\FoodMass;

class FoodIngredient implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

}