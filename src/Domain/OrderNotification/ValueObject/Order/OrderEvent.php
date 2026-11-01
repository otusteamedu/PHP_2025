<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Order;

enum OrderEvent: string
{
    case Created = 'created';
    case Paid = 'paid';
    case Shipped = 'shipped';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
