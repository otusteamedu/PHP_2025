<?php

namespace App\Domain\Entity;


use App\Domain\ValueObject\EventPriority;

class Event
{
    /**
     * @var positive-int
     */
    protected int $id;

    /**
     * @var EventPriority
     */
    protected EventPriority $priority;

    /**
     * @var Condition[]
     */
    protected array $conditions = [];
    /**
     * @var EventInfo
     */
    protected EventInfo $eventInfo;

    /**
     * @param EventPriority $priority
     * @param EventInfo $eventInfo
     */
    function __construct(EventPriority $priority, EventInfo $eventInfo)
    {
        $this->priority = $priority;
        $this->eventInfo = $eventInfo;
        $this->id = $this->eventInfo->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return EventInfo
     */
    public function getEventInfo(): EventInfo
    {
        return $this->eventInfo;
    }

    /**
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param Condition $conditions
     * @return void
     */
    public function setCondition(Condition $conditions): void
    {
        $this->conditions[] = $conditions;
    }

    public function getPriority(): EventPriority
    {
        return $this->priority;
    }
}
