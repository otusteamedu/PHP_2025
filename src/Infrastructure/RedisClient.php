<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Redis;

class RedisClient
{
    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();

        $this->redis->connect(
            'redis',
            6379
        );

        $this->redis->auth(getenv('REDIS_PASSWORD'));
    }

    public function getConnection(): Redis
    {
        return $this->redis;
    }
}