<?php

declare(strict_types=1);

namespace App\Enum;

enum EventStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
