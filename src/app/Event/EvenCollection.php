<?php

declare(strict_types=1);

namespace App\Event;

class EventCollection
{
    private array $events = [];

    public function add(Event $event): void
    {
        $this->events[] = $event;
    }

    public function getAll(): array
    {
        return $this->events;
    }

    public function clear(): void
    {
        $this->events = [];
    }

    public function findBestMatch(array $params): ?Event
    {
        $matchingEvents = [];

        foreach ($this->events as $event) {
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
}