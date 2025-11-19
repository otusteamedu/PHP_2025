<?php

declare(strict_types=1);

namespace App\Domain\Task;

enum Status: string
{
    case Send = 'create';

    case Done = 'done';

    case Waiting = 'waiting';

    case Failed = 'failed';
}
