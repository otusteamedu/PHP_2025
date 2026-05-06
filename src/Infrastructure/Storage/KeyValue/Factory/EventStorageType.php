<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Factory;

enum EventStorageType: string
{
    case Redis = 'redis';
    case Memcached = 'memcached';
}
