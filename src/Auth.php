<?php

declare(strict_types=1);

namespace User\Php2025;

use Redis;

class Auth
{
    public function authenticate(): void
    {
        $redisHost = getenv('REDIS_HOST');
        $redisPort = getenv('REDIS_PORT');

        $redis = new Redis();
        $redis->connect($redisHost, (int)$redisPort);
        $redis->set('auth:' . 'marina', 'ok', 3600);
    }
}
