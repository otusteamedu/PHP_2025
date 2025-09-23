<?php
declare(strict_types=1);

namespace App\EventSystem\Redis;

use App\EventSystem\EventSystemRepository;
use App\Redis\Redis;

final readonly class RedisEventSystemRepository implements EventSystemRepository
{
    private const string ZSET_KEY = 'events:by_priority';
    public function __construct(private Redis $redis)
    {
    }

    public function add(int $priority, array $conditions, array $event): string
    {
        $id = bin2hex(random_bytes(16));
        $payload = json_encode([
            'priority' => $priority,
            'conditions' => $conditions,
            'event' => $event,
        ], JSON_THROW_ON_ERROR);
        $this->redis->client()->set($id, $payload);
        $this->redis->client()->zAdd(self::ZSET_KEY, $priority, $id);

        return $id;
    }

    public function get(string $id): ?array
    {
        $payload = $this->redis->client()->get($id);
        if ($payload === false || $payload === null) {
            return null;
        }


        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getEventIdsByPriorityDesc(): array
    {
        $ids = $this->redis->client()->zRevRange(self::ZSET_KEY, 0, -1);
        return $ids ?: [];
    }

    public function clearAll()
    {
        $this->redis->client()->del(self::ZSET_KEY);
    }
}
