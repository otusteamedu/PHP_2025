<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum TaskStatus: string
{
    case New = 'new';
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
