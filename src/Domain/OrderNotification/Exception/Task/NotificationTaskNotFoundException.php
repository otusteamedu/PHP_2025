<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Task;

use App\Domain\OrderNotification\Exception\TerminalFailure;
use App\Domain\Shared\Exception\ResourceNotFoundException;

class NotificationTaskNotFoundException extends ResourceNotFoundException implements TerminalFailure
{
    public static function byId(int $id): self
    {
        return new self(sprintf('Задача уведомления № %d не найдена.', $id));
    }
}
