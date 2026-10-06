<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Producer;

enum ExchangeType: string
{
    case Direct = 'direct';
    case Fanout = 'fanout';
    case Topic = 'topic';
    case Headers = 'headers';
}
