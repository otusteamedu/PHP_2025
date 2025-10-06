<?php

declare(strict_types=1);

namespace App\Event;

class Event
{
    public function __construct(
        private int $priority,
        private array $conditions,
        private array $eventData
    ) {}

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }

    public function matches(array $params): bool
    {
        foreach ($this->conditions as $key => $value) {
            if (!isset($params[$key]) || $params[$key] != $value) {
                return false;
            }
        }
        return true;
    }

    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'conditions' => $this->conditions,
            'event' => $this->eventData
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['priority'],
            $data['conditions'],
            $data['event']
        );
    }
}