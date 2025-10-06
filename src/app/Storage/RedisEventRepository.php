<?php

declare(strict_types=1);

namespace App\Storage;

use App\Event\Event;
use App\Event\EventRepositoryInterface;
use Redis;

class RedisEventRepository implements EventRepositoryInterface
{
    private const REDIS_KEY = 'events';

    public function __construct(private Redis $redis) {}

    public function save(Event $event): void
    {
        $events = $this->getAllEvents();
        $events[] = $event->toArray();
        $this->saveAllEvents($events);
    }

    public function findAll(): array
    {
        $eventsData = $this->getAllEvents();
        $events = [];

        foreach ($eventsData as $eventData) {
            $events[] = Event::fromArray($eventData);
        }

        return $events;
    }

    public function clear(): void
    {
        $this->redis->del(self::REDIS_KEY);
    }

    public function findBestMatch(array $params): ?Event
    {
        $events = $this->findAll();
        $matchingEvents = [];

        foreach ($events as $event) {
            if ($event->matches($params)) {
                $matchingEvents[] = $event;
            }
        }

        if (empty($matchingEvents)) {
            return null;
        }

        usort($matchingEvents, fn($a, $b) => $b->getPriority() <=> $a->getPriority());

        return $matchingEvents[0];
    }

    private function getAllEvents(): array
    {
        $data = $this->redis->get(self::REDIS_KEY);
        return $data ? json_decode($data, true) : [];
    }

    private function saveAllEvents(array $events): void
    {
        $this->redis->set(self::REDIS_KEY, json_encode($events));
    }
}