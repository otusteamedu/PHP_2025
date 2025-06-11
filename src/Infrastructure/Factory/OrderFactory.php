<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Infrastructure\Order\Order;

final class OrderFactory
{
    public static function createOrder(array $products): Order
    {
        return new Order($products);
    }
}
