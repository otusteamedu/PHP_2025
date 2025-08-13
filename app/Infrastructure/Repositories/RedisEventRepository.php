<?php

namespace App\Infrastructure\Repositories;

use App\Application\EventRepositoryInterface;
use App\Domain\Entities\Event;
use App\Domain\ValueObjects\Conditions;
use App\Domain\ValueObjects\EventName;
use App\Domain\ValueObjects\Priority;
use Redis;

class RedisEventRepository implements EventRepositoryInterface
{
    private $redis;
    
    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
    }

    public function save(Event $event): void
    {
        $conditions = $event->getConditions()->toArray();
        $conditions1 = [];
        foreach ($conditions as $key => $val) {
            $conditions1[] = $key . ':' . $val;
        }
        $conditionStr = implode('::', $conditions1);

        $this->redis->hSet('events', $event->getEventName()->toString(), $conditionStr);
        $this->redis->zAdd('priorities', $event->getPriority()->toInt(), $event->getEventName()->toString());
    }

    public function fetchAll(): array
    {
        $eventData = $this->redis->hGetAll('events');
        $priorityData = $this->redis->zRange('priorities', 0, -1, true);

        $events = [];
        foreach ($eventData as $ev => $conditions) {
            $arr = explode('::', $conditions);
            $preparedConditions = [];
            foreach ($arr as $val) {
                $temp = explode(':', $val);
                $preparedConditions[$temp[0]] = $temp[1];
            }
            $events[] = new Event(
                new EventName($ev),
                new Priority($priorityData[$ev]),
                new Conditions($preparedConditions)
            );
        }
        return $events;
    }

    public function deleteAll(): void
    {
        $this->redis->del('events');
        $this->redis->del('priorities');
    }
}