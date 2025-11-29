<?php declare(strict_types=1);

namespace EManager\Storage;

use Redis;

class RedisStorage implements StorageInterface
{
    private Redis $redis;
    private string $eventsKey = 'events';

    public function __construct(string $host = 'redis', int $port = 6379)
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
    }

    public function addEvent(array $event): void
    {
        // Сериализуем событие для хранения
        $serializedEvent = serialize($event);

        // Сохраняем в Redis с использованием priority как score в сортированном множестве
        $this->redis->zAdd($this->eventsKey, $event['priority'], $serializedEvent);
    }

    public function clearEvents(): void
    {
        $this->redis->del($this->eventsKey);
    }

    public function findMatchingEvent(array $matching): ?array
    {
        if (!isset($matching["params"]) || !is_array($matching['params']))
        {
            return null;
        }

        // Получаем все события, отсортированные по приоритету (от высокого к низкому)
        $events = $this->redis->zRevRange($this->eventsKey, 0, -1);

        foreach ($events as $serializedEvent) {
            $event = unserialize($serializedEvent);

            if ($this->matchesConditions($event['conditions'], $matching["params"])) {
                return $event;
            }
        }

        return null;
    }

    private function matchesConditions(array $conditions, array $params): bool
    {
        foreach ($conditions as $key => $value)
        {
            if (!isset($params[$key]) || $params[$key] != $value)
            {
                return false;
            }
        }

        return true;
    }
}