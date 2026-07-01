<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Redis;

class RedisClient
{
    private Redis $redis;

    public function __construct(string $host, int $port, string $password = '')
    {
        $this->redis = new Redis();

        $this->redis->connect(
            host: $host,
            port: $port,
        );

        if (!empty($password)) {
            $this->redis->auth($password);
        }
    }

    public function getConnection(): Redis
    {
        return $this->redis;
    }
}