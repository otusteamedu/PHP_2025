<?php

namespace App\Storage;

class RedisStore implements KeyValueStoreInterface, RedisSpecificOperationsInterface
{
    private \Redis $redis;

    public function __construct()
    {
        $this->redis = new \Redis();

        if (!$this->redis->connect($_ENV['REDIS_HOST'], (int)$_ENV['REDIS_PORT'])) {
            throw new \RuntimeException('Failed to connect to Redis');
        }
    }

    public function getClient(): \Redis
    {
        return $this->redis;
    }

    public function set(string $key, string $value): bool
    {
        return $this->redis->set($key, $value);
    }

    public function get(string $key): ?string
    {
        return $this->redis->get($key);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key);
    }

    public function exists(string $key): bool
    {
        return $this->redis->exists($key);
    }

    public function zAdd(string $key, array|float $score, mixed $data): int|float|false
    {
        return $this->redis->zAdd($key, $score, $data);
    }

    public function zRevRange(string $key, int $start, int $end, mixed $scores = null): array|false
    {
        return $this->redis->zRevRange($key, $start, $end, $scores);
    }
}
