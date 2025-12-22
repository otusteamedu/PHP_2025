<?php

namespace App\Storage;

use App\Interfaces\EventStorageInterface;
use App\Models\Event;

/**
 * Хранилище событий в памяти
 */
class MemoryStorage implements EventStorageInterface
{
    /**
     * Массив событий, ключ - ID события
     */
    private array $events = [];

    public function add(Event $event): string
    {
        $id = uniqid('mem_', true);
        $this->events[$id] = new Event(
            $event->priority,
            $event->conditions,
            $event->eventData,
            $id
        );
        return $id;
    }

    public function getAll(): array
    {
        return array_values($this->events);
    }

    public function clearAll(): bool
    {
        $this->events = [];
        return true;
    }

    public function findBestMatch(array $params): ?Event
    {
        $bestEvent = null;

        foreach ($this->events as $event) {
            if ($event->matches($params)) {
                if ($bestEvent === null || $event->priority > $bestEvent->priority) {
                    $bestEvent = $event;
                }
            }
        }

        return $bestEvent;
    }
}