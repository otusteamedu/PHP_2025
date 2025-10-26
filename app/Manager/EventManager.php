<?php

declare(strict_types=1);

namespace App\Manager;

use App\Contracts\EventStorageInterface;
use App\Models\Event;

class EventManager
{
    
    public function __construct(private EventStorageInterface $storage){}

    /**
     * Добавить новое событие в систему
     */
    public function addEvent(int $priority, array $conditions, array $eventData): bool
    {
        $event = new Event($priority, $conditions, $eventData);
        return $this->storage->addEvent($event);
    }

    /**
     * Очистить все события
     */
    public function clearAllEvents(): bool
    {
        return $this->storage->clearAllEvents();
    }

    /**
     * Найти наиболее подходящее событие для параметров пользователя
     */
    public function findBestEvent(array $params): ?Event
    {
        return $this->storage->findBestMatchingEvent($params);
    }

    /**
     * Получить все события
     */
    public function getAllEvents(): array
    {
        return $this->storage->getAllEvents();
    }
} 