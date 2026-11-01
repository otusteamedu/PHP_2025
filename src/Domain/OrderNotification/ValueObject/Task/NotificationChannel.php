<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Task;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
