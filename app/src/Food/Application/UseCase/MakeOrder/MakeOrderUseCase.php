<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeOrder;

use App\Food\Application\UseCase\MakeBurger\MakeBurgerRequest;

class MakeOrderUseCase
{
    public function __construct()
    {
    }

    public function __invoke(MakeBurgerRequest $makeBurgerRequest): MakeOrderResponse
    {
        // TODO: Implement __invoke() method.
    }
}