<?php declare(strict_types=1);

namespace EManager;

use EManager\Storage\StorageInterface;

class EventManager
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function addEvent(array $eventData): void
    {
        // Валидация данных события
        if (!isset($eventData['priority'], $eventData['conditions'], $eventData['event'])) {
            throw new \InvalidArgumentException('Invalid event structure');
        }

        $this->storage->addEvent($eventData);
    }

    public function clearEvents(): void
    {
        $this->storage->clearEvents();
    }

    public function findBestMatchingEvent(array $params): ?array
    {
        return $this->storage->findMatchingEvent($params);
    }
}