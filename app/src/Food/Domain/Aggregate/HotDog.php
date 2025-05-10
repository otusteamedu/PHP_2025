<?php

declare(strict_types=1);

namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodTitle;

class HotDog extends Food
{
    public function __construct(FoodTitle $title)
    {
        parent::__construct($title);
    }
}
