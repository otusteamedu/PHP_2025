<?php

namespace Blarkinov\RedisCourse\Models\Event;

use Blarkinov\RedisCourse\Service\Redis;
use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;

class WorkerEvent
{
    private const KEY_STORAGE_EVENT = "storage:event";
    private const KEY_PRIORITY_EVENT = "priority:event";

    public static function save(Event $event)
    {
        $serialize = serialize($event);
        $redis = new Redis();
        $redis->hset(self::KEY_STORAGE_EVENT, $event->getUuid(), $serialize);
        $redis->zadd(self::KEY_PRIORITY_EVENT, [$event->getUuid() => $event->getPriority()]);
    }

    public static function getPriorityEvents(?array $conditions = null): ?Event
    {
        $redis = new Redis();
        $events = $redis->zrange(self::KEY_PRIORITY_EVENT, true, true);

        return self::filter($events, $conditions);
    }

    public static function getAllEvents(): array
    {
        $redis = new Redis();
        $data = $redis->zrange(self::KEY_PRIORITY_EVENT, true, true);
        $events = [];
        foreach ($data as $uuid => $event) {
            $events[$uuid] = unserialize($redis->hget(self::KEY_STORAGE_EVENT, $uuid));
        }

        return $events;
    }

    public static function createEvent(int $priority, array $conditions, array $eventData): Event
    {
        $uuid = Uuid::uuid4();
        return new Event($uuid->toString(), $priority, $conditions, $eventData);
    }

    private static function filter(array $events, ?array $conditions = null): ?Event
    {
        $redis = new Redis();
        foreach ($events as $uuid => $event) {
            $data = $redis->hget(self::KEY_STORAGE_EVENT, $uuid);
            if (empty($data))
                throw new Exception('not found uuid');

            if (empty($conditions)) {
                return unserialize($data);
            }

            $data = unserialize($data);

            if ($data->validateConditions($conditions))
                return $data;
        }

        return null;
    }
}
