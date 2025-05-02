<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

class MakeBurgerResponse
{
    public function __construct(public string $burger_id)
    {
    }

}