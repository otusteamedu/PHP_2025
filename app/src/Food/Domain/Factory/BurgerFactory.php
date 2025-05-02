<?php
declare(strict_types=1);


namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Burger;
use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class BurgerFactory implements FoodFactoryInterface
{
    public function make(string $title): Food
    {
        return new Burger(new FoodTitle($title));
    }
}