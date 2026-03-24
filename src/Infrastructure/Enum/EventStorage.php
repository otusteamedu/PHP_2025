<?php

declare(strict_types=1);

namespace App\Infrastructure\Enum;

enum EventStorage: string
{
    case Redis = 'redis';
    case Memcached = 'memcached';
}
