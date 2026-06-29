<?php

declare(strict_types=1);

namespace App\Domain;

class Event
{
    public function __construct(
        private int $id,
        private int $priority,
        private array $conditions,
        private array $eventData
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

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
}