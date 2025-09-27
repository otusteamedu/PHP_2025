<?php

declare(strict_types=1);

namespace App\EventService;

use Exception;
use JsonException;
use Redis;

class EventRedisService implements EventServiceInterface
{
    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(getenv('REDIS_HOST'), (int) getenv('REDIS_PORT'));
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addJsonEvent(string $eventName, array $data, mixed $priority): void
    {
        $encodedData = json_encode($data, JSON_THROW_ON_ERROR);
        $this->addEvent($eventName, $encodedData, $priority);
    }

    /**
     * @throws Exception
     */
    public function addEvent(string $eventName, mixed $data, mixed $priority): void
    {
        if (!in_array($eventName, self::EVENT_NAME_LIST, true)) {
            throw new Exception('Invalid event name: ' . $eventName);
        }

        $this->redis->zAdd($eventName, $priority, $data);
    }

    public function clearAllEvents(): void
    {
        $this->redis->del(self::EVENT_NAME_LIST);
    }

    public function getEventListByEventName(string $eventName): ?array
    {
        return $this->redis->zRevRange($eventName, 0, -1);
    }
}
