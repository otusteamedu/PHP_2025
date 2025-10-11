<?php

namespace Blarkinov\RedisCourse\Models\Event;

class Event
{
    public function __construct(
        private string $uuid,
        private int $priority,
        private array $conditions,
        private array $data
    ) {}

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function validateConditions(array $conditions): bool
    {
        foreach ($conditions as $param => $value) {

            if (!isset($this->conditions[$param]))
                return false;

            if ($this->conditions[$param] !== $value)
                return false;
        }
        return true;
    }
}
