<?php

declare(strict_types=1);

namespace App\Storage;

use App\Domain\Event;
use JsonException;
use Predis\ClientInterface;
use RuntimeException;

final class RedisEventStorage implements EventStorageInterface
{
    private const EVENT_IDS_KEY = 'events:ids';
    private const CONDITION_KEYS_KEY = 'events:condition-keys';

    public function __construct(private readonly ClientInterface $redis)
    {
    }

    public function add(Event $event): void
    {
        try {
            $conditionKey = ConditionKey::fromConditions($event->conditions);
            $eventKey = $this->eventKey($event->id);
            $eventJson = json_encode($event->toArray(), JSON_THROW_ON_ERROR);

            $this->redis->transaction(function ($transaction) use ($conditionKey, $event, $eventKey, $eventJson): void {
                $transaction->set($eventKey, $eventJson);
                $transaction->zadd($conditionKey, $event->priority, $event->id);
                $transaction->sadd(self::EVENT_IDS_KEY, $event->id);
                $transaction->sadd(self::CONDITION_KEYS_KEY, $conditionKey);
            });
        } catch (JsonException $exception) {
            throw new RuntimeException('Не удалось сериализовать событие.', previous: $exception);
        }
    }

    public function clear(): void
    {
        $eventIds = $this->redis->smembers(self::EVENT_IDS_KEY);
        $conditionKeys = $this->redis->smembers(self::CONDITION_KEYS_KEY);

        $this->redis->transaction(function ($transaction) use ($eventIds, $conditionKeys): void {
            foreach ($eventIds as $id) {
                $transaction->del($this->eventKey((string) $id));
            }

            foreach ($conditionKeys as $key) {
                $transaction->del((string) $key);
            }

            $transaction->del(self::EVENT_IDS_KEY);
            $transaction->del(self::CONDITION_KEYS_KEY);
        });
    }

    public function findBest(array $params): ?Event
    {
        $bestId = null;
        $bestPriority = null;

        foreach (ConditionKey::allSubsets($params) as $conditions) {
            $topEvent = $this->redis->zrevrange(
                ConditionKey::fromConditions($conditions),
                0,
                0,
                'WITHSCORES',
            );

            foreach ($topEvent as $id => $priority) {
                if ($bestPriority === null || (int) $priority > $bestPriority) {
                    $bestId = (string) $id;
                    $bestPriority = (int) $priority;
                }
            }
        }

        if ($bestId === null) {
            return null;
        }

        $eventJson = $this->redis->get($this->eventKey($bestId));
        if ($eventJson === null) {
            throw new RuntimeException(sprintf('Событие "%s" отсутствует в Redis.', $bestId));
        }

        try {
            $event = json_decode($eventJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('В Redis сохранено некорректное событие.', previous: $exception);
        }

        if (!is_array($event)) {
            throw new RuntimeException('В Redis сохранено некорректное событие.');
        }

        return Event::fromArray($event);
    }

    private function eventKey(string $id): string
    {
        return 'events:data:' . $id;
    }
}
