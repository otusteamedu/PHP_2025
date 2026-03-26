<?php

declare(strict_types=1);

namespace Api\Domain\Enums;

enum RequestStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
