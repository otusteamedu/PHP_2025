<?php

namespace Arlex2305k\Redis\Storage;

class RedisStorage implements StorageInterface
{
    private \Redis $redis;
    private string $eventsKey = 'events';

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function storeEvent(int $priority, array $conditions, array $eventData): void
    {
        $eventId = uniqid('event_');
        $event = [
            'id' => $eventId,
            'priority' => $priority,
            'conditions' => $conditions,
            'event_data' => $eventData
        ];
        $this->redis->hSet($this->eventsKey, $eventId, json_encode($event));
    }

    public function clearEvents(): void
    {
        $this->redis->del($this->eventsKey);
    }

    public function findMatches(array $params): ?array
    {
        $allEvents = $this->getAllEvents();
        if (empty($allEvents)) {
            return null;
        }
        $matchingEvents = [];

        foreach ($allEvents as $event) {
            $matches = true;

            foreach ($event['conditions'] as $conditionKey => $conditionValue) {
                if (!isset($params[$conditionKey]) || $params[$conditionKey] !== $conditionValue) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $matchingEvents[] = $event;
            }
        }

        if (empty($matchingEvents)) {
            return null;
        }
        usort($matchingEvents, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        return $matchingEvents[0];
    }

    public function getAllEvents(): array
    {
        $events = [];
        $storedEvents = $this->redis->hGetAll($this->eventsKey);
        foreach ($storedEvents as $eventJson) {
            $event = json_decode($eventJson, true);
            if ($event !== null) {
                $events[] = $event;
            }
        }
        return $events;
    }
}
