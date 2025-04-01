<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Class Event
 * @package App\Models
 */
class Event
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var int
     */
    private int $priority;
    /**
     * @var Condition[]
     */
    private array $conditions = [];
    /**
     * @var EventInfo
     */
    private EventInfo $eventInfo;

    /**
     * @param int $priority
     * @param array $eventInfo
     * @param array $conditions
     */
    public function __construct(int $priority, array $eventInfo, array $conditions)
    {
        if ($priority < 0) {
            throw new InvalidArgumentException('Event priority must not be less then zero.');
        }

        if (empty($eventInfo)) {
            throw new InvalidArgumentException('Event info must not be empty.');
        }

        if (empty($conditions)) {
            throw new InvalidArgumentException('Event params must not be empty.');
        }

        $this->eventInfo = new EventInfo(
            $eventInfo['id'] ?? 0,
            $eventInfo['name'] ?? ''
        );
        $this->id = $this->eventInfo->getId();
        $this->priority = $priority;

        foreach ($conditions as $name => $value) {
            $this->conditions[] = new Condition($name, (string)$value);
        }
    }

    /**
     * @param string $json
     * @return self
     */
    public static function createFromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!$data) {
            throw new InvalidArgumentException('Incorrect json');
        }

        return new Event(
            $data['priority'] ?? -1,
            $data['event'] ?? [],
            $data['conditions'] ?? []
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'priority' => $this->getPriority(),
            'event' => $this->getEventInfo()->toArray(),
            'conditions' => $this->getPreparedConditions(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getPreparedConditions(): array
    {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            $conditions[$condition->getName()] = $condition->getValue();
        }

        return $conditions;
    }

    /**
     * @return EventInfo
     */
    public function getEventInfo(): EventInfo
    {
        return $this->eventInfo;
    }
}
