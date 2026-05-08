<?php

declare(strict_types=1);

namespace App\Infrastructure\Redis;

use App\Domain\Event\Event;
use App\Domain\Storage\EventStorageInterface;
use Redis;
use RuntimeException;

/**
 * Redis-реализация хранилища событий
 */
final class RedisEventStorage implements EventStorageInterface
{
    private const EVENT_KEYS_SET_NAME = 'events:condition_keys';

    public function __construct(
        private readonly Redis $redis,
        private readonly RedisConditionKeyBuilder $keyBuilder = new RedisConditionKeyBuilder(),
    )
    {
    }

    /**
     * Сохраняет событие в sorted set по ключу его условий
     *
     * Дополнительно сохраняет имя созданного ключа в отдельное множество
     * Это нужно, чтобы при очистке удалить только ключи событий и не трогать другие данные в Redis
     */
    public function add(Event $event): void
    {
        $conditionsKey = $this->keyBuilder->buildKey($event->conditions()->all());
        $rawEvent = json_encode($event->toArray(), JSON_THROW_ON_ERROR);

        $this->redis->multi();

        try {
            $this->redis->zAdd($conditionsKey, $event->priority(), $rawEvent);
            $this->redis->sAdd(self::EVENT_KEYS_SET_NAME, $conditionsKey);
            $this->redis->exec();
        } catch (\Throwable $exception) {
            $this->redis->discard();

            throw $exception;
        }
    }

    /**
     * Удаляет все Redis-ключи, созданные хранилищем событий
     */
    public function clear(): void
    {
        $keys = $this->redis->sMembers(self::EVENT_KEYS_SET_NAME);

        if ($keys !== false && $keys !== []) {
            $this->redis->del($keys);
        }

        $this->redis->del(self::EVENT_KEYS_SET_NAME);
    }

    /**
     * Ищет событие с максимальным приоритетом среди ключей, построенных из параметров запроса
     */
    public function findEventByParamsWithMaxPriority(array $params): ?Event
    {
        $conditionsKeys = $this->keyBuilder->buildKeysForAllSubsets($params);

        if ($conditionsKeys === []) {
            return null;
        }

        $maxPriorityEvent = null;
        $maxPriority = null;

        foreach ($conditionsKeys as $conditionsKey) {
            $rawEvents = $this->redis->zRevRange($conditionsKey, 0, 0, true);

            if ($rawEvents === false || $rawEvents === []) {
                continue;
            }

            foreach ($rawEvents as $rawEvent => $priority) {
                if ($maxPriority === null || $priority > $maxPriority) {
                    $maxPriority = $priority;
                    $maxPriorityEvent = $rawEvent;
                }
            }
        }

        if ($maxPriorityEvent === null) {
            return null;
        }

        return $this->createEventFromJson($maxPriorityEvent);
    }

    /**
     * Восстанавливает доменное событие из JSON, сохраненного в Redis
     */
    private function createEventFromJson(string $rawEvent): Event
    {
        $data = json_decode($rawEvent, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new RuntimeException('Stored event is invalid JSON object.');
        }

        return Event::fromArray($data);
    }
}
