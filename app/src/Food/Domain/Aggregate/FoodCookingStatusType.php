<?php
declare(strict_types=1);

namespace App\Food\Domain\Aggregate;

enum FoodCookingStatusType: string
{
    case IN_QUEUE = 'in_queue';
    case STARTED = 'started';
    case FINISHED = 'finished';
    case PACKED = 'packed';
}