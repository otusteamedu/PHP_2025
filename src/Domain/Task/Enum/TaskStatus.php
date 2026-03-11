<?php
declare(strict_types=1);

namespace App\Domain\Task\Enum;

enum TaskStatus: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
}
