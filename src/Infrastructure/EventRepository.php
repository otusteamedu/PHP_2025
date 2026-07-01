<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Event;
use App\Domain\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(
        private readonly RedisClient $redis
    ) {
    }

    /**
     * @throws \RedisException
     */
    public function nextId(): int
    {
        return (int)$this->redis
            ->getConnection()
            ->incr('event:id');
    }

    /**
     * @throws \RedisException
     */
    public function save(Event $event): void
    {
        $this->redis
            ->getConnection()
            ->set(
                'event:' . $event->getId(),
                json_encode([
                    'id' => $event->getId(),
                    'priority' => $event->getPriority(),
                    'conditions' => $event->getConditions(),
                    'event' => $event->getEventData(),
                ])
            );

        foreach ($event->getConditions() as $name => $value) {
            $this->redis
                ->getConnection()
                ->sAdd(
                sprintf('condition:%s:%s', $name, $value),
                $event->getId()
            );
        }

        $this->redis
            ->getConnection()
            ->zAdd(
                'event:priority',
                $event->getPriority(),
                (string)$event->getId()
            );
    }

    /**
     * @throws \RedisException
     */
    public function clear(): void
    {
        foreach (['event:*', 'condition:*' ] as $pattern)
        {
            $this->deleteByPattern($pattern);
        }
    }

    public function deleteByPattern(string $pattern):void
    {
        $redis = $this->redis->getConnection();

        $iterator = null;

        while ($keys = $redis->scan($iterator,$pattern)) {
            $redis->del(...$keys);
        }
    }

    /**
     * @throws \RedisException
     */
    public function findByParams(array $params): ?Event
    {
        $redis = $this->redis->getConnection();

        $conditionKeys = [];

        foreach ($params as $name => $value) {
            $conditionKeys[] = sprintf(
                'condition:%s:%s',
                $name,
                $value
            );
        }

        $eventIds = $redis->sInter(...$conditionKeys);

        if ($eventIds === []) {
            return null;
        }

        $eventKeys = [];

        foreach ($eventIds as $id) {
            $eventKeys[] = 'event:' . $id;
        }

        $events = $redis->mGet($eventKeys);

        $bestEvent = null;
        $bestPriority = PHP_INT_MIN;

        foreach ($events as $json) {

            if ($json === false) {
                continue;
            }

            $data = json_decode(
                $json,
                true,
            );

            if ($data === null || $data === []) { //Данные о событие пусты
                continue;
            }
            
            if (!empty($data['priority']) && $data['priority'] > $bestPriority) {
                $bestPriority = (int)$data['priority'];
                $bestEvent = $data;
            }
        }

        if ($bestEvent === null) {
            return null;
        }

        return new Event(
            $bestEvent['id'],
            $bestEvent['priority'],
            $bestEvent['conditions'],
            $bestEvent['event'],
        );
    }
}