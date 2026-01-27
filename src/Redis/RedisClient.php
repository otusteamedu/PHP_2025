<?php

namespace App\Redis;

use App\Base\Singleton;
use Redis;

class RedisClient extends Singleton
{
    protected Redis $redis;

    protected function __construct()
    {
        $this->redis = new Redis([
            'host' => $_ENV['REDIS_HOST'],
            'port' => (int)$_ENV['REDIS_PORT'],
            'connectTimeout' => 30,
            'database' => (int)$_ENV['REDIS_DB'],
        ]);

    }

    public function getClient(): Redis
    {
        return $this->redis;
    }
}