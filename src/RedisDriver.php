<?php

declare(strict_types=1);

namespace App;

class RedisDriver
{
    private readonly \Redis $redisHandler;

    public function __construct()
    {
        $this->redisHandler = new \Redis();
        if (!$this->redisHandler->connect('redis')) {
            throw new \RuntimeException('Connection failed');
        }
    }

    public function getVersion(): string
    {
        return $this->redisHandler->info('Server')['redis_version'];
    }
}
