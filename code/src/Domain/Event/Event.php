<?php

declare(strict_types=1);

namespace App\Domain\Event;

use InvalidArgumentException;

/**
 * Доменная модель события с приоритетом, условиями возникновения и данными события
 */
final class Event
{
    /**
     * @param array<string, mixed> $eventPayload
     */
    public function __construct(
        private readonly int $priority,
        private readonly EventConditions $conditions,
        private readonly array $eventPayload,
    ) {
        if ($eventPayload === []) {
            throw new InvalidArgumentException('Event payload cannot be empty.');
        }
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function conditions(): EventConditions
    {
        return $this->conditions;
    }

    /**
     * @return array<string, mixed>
     */
    public function eventPayload(): array
    {
        return $this->eventPayload;
    }

    /**
     * @return array{priority: int, conditions: array<string, mixed>, event: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'conditions' => $this->conditions->all(),
            'event' => $this->eventPayload,
        ];
    }

    /**
     * Восстанавливает событие из массива, полученного из хранилища
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['priority']) || !is_int($data['priority'])) {
            throw new InvalidArgumentException('Event priority must be an integer.');
        }

        if (!isset($data['conditions']) || !is_array($data['conditions'])) {
            throw new InvalidArgumentException('Event conditions must be an array.');
        }

        if (!isset($data['event']) || !is_array($data['event'])) {
            throw new InvalidArgumentException('Event payload must be an array.');
        }

        return new self(
            $data['priority'],
            new EventConditions($data['conditions']),
            $data['event'],
        );
    }
}
