<?php

namespace Crowley\App\Application\Services;

use Redis;

class EventStorage
{

    private Redis $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
    }

    public function addEvent(int $priority, array $conditions, array $event): string
    {

        // Номер события
        $eventId = $this->redis->incr("event_id");
        $key = "event_{$eventId}";

        $this->redis->hMset($key, [
            "priority" => $priority,
            "conditions" => json_encode($conditions),
            "event" => json_encode($event)
        ]);

        $this->redis->sAdd("events", $key);
        return $key;

    }

    public function getEvents(): array {

        $keys = $this->redis->sMembers("events");
        $events = [];

        foreach ($keys as $key) {
            $event = $this->redis->hGetAll($key);

            $event['conditions'] = json_decode($event['conditions'], true);
            $event['event'] = json_decode($event['event'], true);

            $events[$key] = $event;
        }

        return $events;

    }

    public function getEvent(array $params): ?array
    {
        $keys = $this->redis->sMembers("events");

        $bestEvent = null;
        $maxPriority = -1;

        foreach ($keys as $key) {
            $event = $this->redis->hGetAll($key);

            $event['conditions'] = json_decode($event['conditions'], true);
            $event['event'] = json_decode($event['event'], true);
            $priority = $event['priority'];


            $match = true;
            foreach ($event['conditions'] as $key => $value) {
                if (!isset($params[$key]) || $params[$key] != $value) {
                    $match = false;
                    break;
                }
            }


            // если условия выполнены и priority выше текущего
            if ($match && $priority > $maxPriority) {
                $maxPriority = $priority;
                $bestEvent = $event['event'];
            }
        }

        return $bestEvent;

    }
    public function clearEvents() : void
    {
        $keys = $this->redis->sMembers('events');
        if ($keys) {
            $this->redis->del(...$keys);
        }
        $this->redis->del('events', 'event_id');
    }

}