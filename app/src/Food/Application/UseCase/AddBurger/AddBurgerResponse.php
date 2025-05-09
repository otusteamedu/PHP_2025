<?php

declare(strict_types=1);

namespace App\Food\Application\UseCase\AddBurger;

class AddBurgerResponse
{
    public function __construct(public string $burger_id)
    {
    }
}
