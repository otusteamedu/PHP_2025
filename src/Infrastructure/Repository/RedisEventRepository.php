<?php

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Repository;

use Kamalo\EventsService\Domain\Entity\Event;
use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;
use Kamalo\EventsService\Domain\Factory\EventFactoryInterface;
use Kamalo\EventsService\Application\Factory\EventFactory;
use Kamalo\EventsService\Infrastucture\Enviropment\Config;

class RedisEventRepository implements EventRepositoryInterface
{
    private \Redis $redis;
    private EventFactoryInterface $factory;
    public function __construct()
    {
        $config = new Config();
        $this->redis = new \Redis();

        try {
            $this->redis->connect(
                $config->get('database', 'host'),
                $config->get('database', 'port')
            );
        } catch (\Throwable $e) {
            throw new \Exception('Ошибка при подключении к Redis: ' . $e->getMessage());
        }

        $this->factory = new EventFactory();

        if (
            !$this->redis->get('last:id')
        ) {
            $this->redis->set('last:id', 0);
        }
    }


    public function findAll(): array
    {
        $keys = $this->getKeys();

        $events = [];

        if (count($keys) === 0) {
            return [];
        }

        foreach ($keys as $key) {

            $data = $this->redis->hGetAll($key);

            if (count($data) > 0) {
                $events[] = $this->factory->createFromArray($data);
            }
        }

        return $events;
    }
    public function findByParams(array $params): ?Event
    {
        $keys = $this->getKeys();

        $events = [];
        $filteredKeys = [];

        if (count($keys) === 0) {
            return null;
        }

        $maxPriority = 0;

        foreach ($keys as $key) {

            $data = null;
            $filteredKeys[$key] = 0;

            foreach ($params as $field => $value) {
                if (
                    $this->redis->hExists($key, 'conditions:' . $field)
                    && $this->redis->hGet($key, 'conditions:' . $field) == $value

                ) {
                    $filteredKeys[$key]++;
                }
            }

            if ($this->redis->hLen($key) - $filteredKeys[$key] == 2) {

                $data = $this->redis->hGetAll($key);

                if (
                    count($data) > 0
                    && $data['priority'] > $maxPriority
                ) {
                    $events[] = $this->factory->createFromArray($data);
                }
            }
        }

        if (count($events) === 0) {
            return null;
        }

        $maxPriorityEvent = $events[0];

        foreach ($events as $event) {
            if ($event->getPriority() > $maxPriorityEvent->getPriority()) {
                $maxPriorityEvent = $event;
            }
        }
        return $maxPriorityEvent;
    }

    public function add(Event $event): void
    {
        $event->setId($this->redis->get('last:id') + 1);

        if (
            !$this->redis->hMSet(
                'event:' . $event->getId(),
                $event->jsonSerialize()
            )
        ) {
            throw new \Exception('Ошибка при добавлении события c' . $event->getId());
        }

        try {
            $this->redis->incr('last:id');
        } catch (\Throwable $e) {
            throw new \Exception('Ошибка при добавлении события c ' . $event->getId());
        }
    }

    public function clear(): void
    {
        try {
            $this->redis->flushAll();
        } catch (\Throwable $e) {
            throw new \Exception("Ошибка при удалении всех событий");
        }

        $this->redis->set('last:id', 0);
    }

    private function getKeys(): array
    {
        $pattern = 'event:*';
        $i = null;

        return $this->redis->scan($i, $pattern);
    }
}
