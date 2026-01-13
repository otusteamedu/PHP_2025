<?php

namespace Arlex2305k\Redis;

use Arlex2305k\Redis\Storage\StorageInterface;

class EventService
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function addEvent(array $eventData): bool
    {
        if (!isset($eventData['priority']) || !isset($eventData['conditions']) || !isset($eventData['event'])) {
            throw new \InvalidArgumentException('Данные события должны содержать поля priority, conditions и event');
        }
        if (!is_int($eventData['priority']) || !is_array($eventData['conditions']) || !is_array($eventData['event'])) {
            throw new \InvalidArgumentException('Неверные типы данных события');
        }

        $priority = $eventData['priority'];
        $conditions = $eventData['conditions'];
        $event = $eventData['event'];

        $this->storage->storeEvent($priority, $conditions, $event);

        return true;
    }

    public function clearEvents(): bool
    {
        $this->storage->clearEvents();
        return true;
    }

    public function getEventByParams(array $params): ?array
    {
        $result = $this->storage->findMatches($params);
        return $result;
    }

    public function getAllEvents(): array
    {
        return $this->storage->getAllEvents();
    }
}
