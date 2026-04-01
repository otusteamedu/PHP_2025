<?php

declare(strict_types=1);

namespace App\Enum;

enum QueueNameEnum: string
{
    case EVENT_QUEUE = 'event_queue';
}
