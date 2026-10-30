<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Task;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
}
