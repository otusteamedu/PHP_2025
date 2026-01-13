<?php

namespace Arlex2305k\Redis\Storage;

class MemcachedStorage implements StorageInterface
{
    private \Memcached $memcached;
    private string $eventsKey = 'events';
    private int $expirationTime = 0;

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    public function storeEvent(int $priority, array $conditions, array $eventData): void
    {
        $eventId = uniqid('event_');
        $allEvents = $this->getAllEvents();
        $event = [
            'id' => $eventId,
            'priority' => $priority,
            'conditions' => $conditions,
            'event_data' => $eventData
        ];
        $allEvents[$eventId] = $event;
        $this->memcached->set($this->eventsKey, $allEvents, $this->expirationTime);
    }

    public function clearEvents(): void
    {
        $this->memcached->delete($this->eventsKey);
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
        $events = $this->memcached->get($this->eventsKey);
        if ($events === false) {
            return [];
        }
        return $events;
    }
}
