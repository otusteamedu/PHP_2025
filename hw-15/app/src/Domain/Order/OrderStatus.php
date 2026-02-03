<?php

declare(strict_types=1);

namespace App\Domain\Order;

enum OrderStatus: string
{
    case Accept = 'accept';
    case Cooking = 'cooking';
    case Ready = 'ready';
}
