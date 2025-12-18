<?php

declare(strict_types=1);

namespace App\Model;

class Event
{
    /**
     * @var int
     */
    protected int $id;
    /**
     * @var int
     */
    protected int $priority;

    /**
     * @var Condition[]
     */
    protected array $conditions = [];
    /**
     * @var EventInfo
     */
    protected EventInfo $eventInfo;

    /**
     * @param int $priority
     * @param array $eventInfo
     * @param array $conditions
     */
    function __construct(int $priority, array $eventInfo, array $conditions)
    {
        if ($priority <= 0) {
            throw new \InvalidArgumentException('$priority должно быть больше 0');
        }

        $this->eventInfo = new EventInfo(
            $eventInfo['id'] ?? 0,
            $eventInfo['name'] ?? '',
        );

        $this->id = $this->eventInfo->getId();
        $this->priority = $priority;
        foreach ($conditions as $key => $condition) {
            $this->conditions[] = new Condition(
                $key ?? '',
                $condition ?? ''
            );
        }
    }

    /**
     * @param array $array
     * @return self
     */
    public static function createFromArray(array $array): self
    {
        if (empty($array)) {
            throw new \InvalidArgumentException('$array пустой');
        }

        return new self(
            $array['priority'] ?? 0,
            $array['event'] ?? [],
            $array['conditions'] ?? []
        );
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
     * @return EventInfo
     */
    public function getEventInfo(): EventInfo
    {
        return $this->eventInfo;
    }

    /**
     * @return string[]
     */
    public function getConditionsToArray(): array
    {
        $conditions = [];

        foreach ($this->conditions as $condition) {
            $conditions = \array_merge($conditions, $condition->toArray());
        }

        return $conditions;
    }

    /** Имена ключей условий
     * @return string[]
     */
    public function getConditionsToRedisData(): array
    {
        $conditions = [];

        foreach ($this->conditions as $condition) {
            $conditions[] = $condition->getKeyForRedis();
        }

        return $conditions;
    }

    /**
     * @return array{
     *     priority: int,
     *     event: array{
     *          id: int,
     *          name: string
     *      },
     *     conditions: array
     * }
     */
    public function toArray(): array
    {
        return [
            'priority' => $this->getPriority(),
            'event' => $this->getEventInfo()->toArray(),
            'conditions' => $this->getConditionsToArray(),
        ];
    }
}
