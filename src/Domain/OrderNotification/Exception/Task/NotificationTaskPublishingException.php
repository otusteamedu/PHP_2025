<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Task;

use App\Domain\Shared\Exception\ServiceUnavailableException;

class NotificationTaskPublishingException extends ServiceUnavailableException
{
    public static function failed(\Throwable $e): self
    {
        return new self('Не удалось передать задачу на обработку.', previous: $e);
    }
}
