<?php

declare(strict_types=1);

namespace User\Php2025;

use Redis;

class Auth
{
    public function authenticate(): void
    {
        $redis = new Redis();
        $redis->connect('redis', 6379);
        $redis->set('auth:' . 'marina', 'ok', 3600);
    }
}
