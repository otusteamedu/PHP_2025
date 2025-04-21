<?php

namespace App\Infrastructure\Mapper;

use App\Domain\Entity\Condition;
use App\Domain\Entity\Event;
use App\Domain\Entity\EventInfo;
use App\Domain\ValueObject\Condition\ConditionName;
use App\Domain\ValueObject\Condition\ConditionValue;
use App\Domain\ValueObject\EventInfoName;
use App\Domain\ValueObject\EventPriority;

class EventMapper
{
    /**
     * @param array $array
     * @return Event
     */
    public static function createFromArray(array $array): Event
    {
        if (empty($array)) {
            throw new \InvalidArgumentException('$array пустой');
        }

        $event = new Event(
            new EventPriority($array['priority']),
            new EventInfo($array['event']['id'], new EventInfoName($array['event']['name']))
        );

        if (isset($array['conditions'])) {
            foreach ($array['conditions'] as $conditionKey => $conditionValue) {
                $condition = new Condition(
                    new ConditionName($conditionKey),
                    new ConditionValue($conditionValue)
                );

                $event->setCondition($condition);
            }
        }

        return $event;
    }

    /**
     * @return array{
     *     priority: int,
     *     event: array{
     *          id: int,
     *          name: string
     *      },
     *     conditions: array {}
     * }
     */
    public static function toArray(Event $event): array
    {
        $conditions = [];

        foreach ($event->getConditions() as $condition) {
            \array_merge($conditions, ConditionMapper::toArray($condition));
        }

        return [
            'priority' => $event->getPriority(),
            'event' => EventInfoMapper::toArray($event->getEventInfo()),
            'conditions' => $conditions,
        ];
    }
}
