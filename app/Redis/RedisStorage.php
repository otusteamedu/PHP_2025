<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Redis;

use Predis\Client;
use Predis\Response\Status;

class RedisStorage implements StorageInterface
{
    private Client $client;

    public function __construct(RedisClient $redisClient)
    {
        $this->client = $redisClient->getClient();
    }

    public function set(string $key, mixed $value, $expireResolution = null, $expireTTL = null, $flag = null): ?Status
    {
        return $this->client->set($key, $value, $expireResolution, $expireTTL, $flag);
    }

    public function get(string $key): string
    {
        return $this->client->get($key) ?? '';
    }

    public function del(string|array $key): int
    {
        return $this->client->del($key);
    }

    public function keys(string $pattern): array
    {
        return $this->client->keys($pattern);
    }

    public function sadd(string $key, array $members): int
    {
        return $this->client->sadd($key, $members);
    }
}
