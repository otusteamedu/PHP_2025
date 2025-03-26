<?php

namespace App\Redis;

use App\EventSystem\StorageInterface;

class RedisStorage implements StorageInterface
{
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function set(string $key, $value)
    {
        return $this->redis->set($key, $value);
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function del(string $key)
    {
        return $this->redis->del($key);
    }

    public function keys(string $pattern)
    {
        return $this->redis->keys($pattern);
    }

    public function sadd(string $key, $value)
    {
        return $this->redis->sadd($key, $value);
    }
}
