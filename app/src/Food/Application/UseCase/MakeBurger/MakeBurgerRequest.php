<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

use App\Food\Domain\Aggregate\FoodIngredient;

class MakeBurgerRequest
{
    public function __construct(public string $title)
    {
    }

}