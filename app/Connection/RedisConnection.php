<?php

namespace App\Connection;

use Exception;
use Redis;

class RedisConnection
{
    /**
     * @return bool
     * @throws Exception
     */
    public static function connect(): bool
    {
        $host = getenv('REDIS_HOST');
        $port = 6379;
        $password = getenv('REDIS_PASSWORD');

        try {
            $redis = new Redis();
            $redis->connect($host, $port);
            $redis->auth($password);
        } catch (\RedisException $e) {
            throw new Exception($e);
        }

        return true;
    }
}