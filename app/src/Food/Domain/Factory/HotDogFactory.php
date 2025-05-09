<?php

declare(strict_types=1);

namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\HotDog;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class HotDogFactory implements FoodFactoryInterface
{
    public function build(string $orderId, string $title): Food
    {
        return new HotDog(new FoodTitle($title));
    }
}
