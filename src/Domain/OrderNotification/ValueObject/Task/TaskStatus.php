<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Task;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Fetching = 'fetching';
    case Sending = 'sending';
    case Retrying = 'retrying';
    case Completed = 'completed';
    case Failed = 'failed';

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Failed], true);
    }
}
