<?php

declare(strict_types=1);

namespace App\Models;

class Event
{
    public function __construct(
        private int $priority,
        private array $conditions,
        private array $eventData,
        private ?string $id = null
    ) {
        $this->id = $this->id ?? uniqid();
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

    public function getId(): string
    {
        return $this->id;
    }

    public function matchesParams(array $params): bool
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
            'id' => $this->id,
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
            $data['event'],
            $data['id'] ?? null
        );
    }
} 