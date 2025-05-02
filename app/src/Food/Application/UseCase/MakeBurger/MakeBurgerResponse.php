<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

use App\Food\Domain\Aggregate\VO\FoodTitle;

class MakeBurgerResponse
{
    public function __construct(public string $burgerId)
    {
    }

}