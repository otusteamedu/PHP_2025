<?php
declare(strict_types=1);


namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Burger\Burger;
use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\VO\FoodTitle;

readonly class BurgerFactory implements FoodFactoryInterface
{
    public function build(?string $title): Food
    {
        if ($title === null) {
            return new Burger();
        }
        return new Burger(new FoodTitle($title));
    }
}