<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Consumer;

enum ConsumeResult: string
{
    case Ack = 'ack';
    case Reject = 'reject';
    case Drop = 'drop';
}
