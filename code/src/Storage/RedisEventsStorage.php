<?php

declare(strict_types=1);

namespace App\Storage;

use Redis;

class RedisEventsStorage implements EventsStorageInterface
{
    use EventsMatcherTrait;

    private const EVENTS_KEY = 'events:list';

    private Redis $client;

    public function __construct(?Redis $client = null)
    {
        if ($client instanceof Redis) {
            $this->client = $client;
            return;
        }

        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = (int)(getenv('REDIS_PORT') ?: 6379);

        $redis = new Redis();
        $redis->connect($host, $port);

        $this->client = $redis;
    }

    public function addEvent(array $event): void
    {
        $this->client->rPush(self::EVENTS_KEY, json_encode($event, JSON_UNESCAPED_UNICODE));
    }

    public function clear(): void
    {
        $this->client->del(self::EVENTS_KEY);
    }

    public function findMatches(array $params): array
    {
        $rawEvents = $this->client->lRange(self::EVENTS_KEY, 0, -1);

        $events = array_map(static function ($rawEvent) {
            return json_decode((string)$rawEvent, true);
        }, $rawEvents);

        return $this->filterAndSort($events, $params);
    }
}
