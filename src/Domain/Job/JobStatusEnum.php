<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Job;

enum JobStatusEnum: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
}
