<?php

declare(strict_types=1);

namespace App\Food\Application\UseCase\UpdateOrderStatus;

class UpdateOrderStatusRequest
{
    public function __construct(public string $orderId, public string $newStatus)
    {
    }
}
