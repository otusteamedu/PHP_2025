<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\PlaceOrder;

class PlaceOrderResponse
{
    public function __construct(public string $order_id)
    {
    }

}