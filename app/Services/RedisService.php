<?php

namespace App\Services;

use Exception;
use Redis;
use RedisException;

class RedisService extends Service
{
    /** @var Redis */
    public Redis $client;

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function connect(): void {
        $host = getenv('REDIS_HOST');
        $port = 6379;
        $password = getenv('REDIS_PASSWORD');

        try {
            $redis = new Redis();
            $redis->connect($host, $port);
            $redis->auth($password);
        } catch (RedisException $e) {
            throw new Exception($e);
        }

        $this->client = $redis;
    }
}