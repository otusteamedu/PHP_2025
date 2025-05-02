<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeOrder;

class MakeOrderRequest
{
    public function __construct(string $foodTitle, string ...$additionalIngredients)
    {
    }

}