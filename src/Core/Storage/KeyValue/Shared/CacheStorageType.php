<?php

declare(strict_types=1);

namespace App\Core\Storage\KeyValue\Shared;

enum CacheStorageType: string
{
    case Redis = 'redis';
    case Memcached = 'memcached';
}
