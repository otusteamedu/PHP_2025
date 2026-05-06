<?php

declare(strict_types=1);

namespace App\Domain\SystemHealth\Enum;

enum ServiceType: string
{
    case POSTGRESQL = 'postgresql';
    case REDIS = 'redis';
    case MEMCACHED = 'memcached';
    case SESSION_STORAGE = 'session_storage';
}
