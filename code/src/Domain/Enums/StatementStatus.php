<?php

declare(strict_types=1);

namespace Queues\Domain\Enums;

enum StatementStatus: string
{
    case PENDING = 'PENDING';
    case SUCCESS = 'SUCCESS';
    case ERROR = 'ERROR';
}
