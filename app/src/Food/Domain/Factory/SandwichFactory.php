<?php

declare(strict_types=1);

namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\Sandwich;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class SandwichFactory implements FoodFactoryInterface
{
    public function build(string $orderId, string $title): Food
    {
        return new Sandwich(new FoodTitle($title));
    }
}
