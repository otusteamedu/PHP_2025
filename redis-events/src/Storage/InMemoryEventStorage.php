<?php

declare(strict_types=1);

namespace App\Storage;

use App\Domain\Event;

final class InMemoryEventStorage implements EventStorageInterface
{
    /** @var array<string, Event> */
    private array $events = [];

    public function add(Event $event): void
    {
        $this->events[$event->id] = $event;
    }

    public function clear(): void
    {
        $this->events = [];
    }

    public function findBest(array $params): ?Event
    {
        $best = null;

        foreach ($this->events as $event) {
            if (!$this->matches($event, $params)) {
                continue;
            }

            if ($best === null || $event->priority > $best->priority) {
                $best = $event;
            }
        }

        return $best;
    }

    /** @param array<string, scalar> $params */
    private function matches(Event $event, array $params): bool
    {
        foreach ($event->conditions as $name => $value) {
            if (!array_key_exists($name, $params)) {
                return false;
            }

            $paramValue = $params[$name];
            $normalizedValue = is_bool($paramValue)
                ? ($paramValue ? 'true' : 'false')
                : (string) $paramValue;

            if ($normalizedValue !== $value) {
                return false;
            }
        }

        return true;
    }
}
