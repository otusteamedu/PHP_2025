<?php

namespace Blarkinov\RedisCourse\Service;

use Predis\Client;

class Redis
{

    private static $instance = null;

    public function __construct()
    {
        if (self::$instance === null) {
            self::$instance = new Client([
                'scheme' => 'tcp',
                'host'   => $_ENV['REDIS_HOST'],
                'port'   => $_ENV['REDIS_PORT'],
            ]);
        }
    }

    public function destroy()
    {
        self::$instance->flushall();
    }

    public function zadd(string $key, array $values)
    {
        self::$instance->zadd($key, $values);
    }

    public function zrange(string $key, bool $withScores = false, bool $reverse = false, ?int $start = null, ?int $end = null)
    {
        if ($start === null)
            $start = 0;
        if ($end === null)
            $end = $this->zcard($key);

        if ($reverse)
            return  $withScores
                ? self::$instance->zrevrange($key, $start, $end, ['WITHSCORES' => true])
                : self::$instance->zrevrange($key, $start, $end);

        return $withScores
            ? self::$instance->zrange($key, $start, $end, ['WITHSCORES' => true])
            : self::$instance->zrange($key, $start, $end);
    }

    public function zcard(string $key)
    {
        return self::$instance->zcard($key);
    }

    public function hset(string $key, string $field, string $value)
    {
        self::$instance->hset($key, $field, $value);
    }

    public function hget(string $key, string $field): ?string
    {
        return self::$instance->hget($key,  $field);
    }

    public function hgetAll(string $key): array
    {
        return  self::$instance->hgetAll($key);
    }
}
