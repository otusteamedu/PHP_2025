<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeOrder;

class MakeOrderResponse
{
    public function __construct(public string $order_id)
    {
    }

}