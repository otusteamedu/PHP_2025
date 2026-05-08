<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Event\Event;
use App\Domain\Storage\EventStorageInterface;

/**
 * Доменный сервис для добавления, очистки и поиска событий
 */
final class EventService
{
    public function __construct(private readonly EventStorageInterface $storage)
    {
    }

    public function add(Event $event): void
    {
        $this->storage->add($event);
    }

    public function clear(): void
    {
        $this->storage->clear();
    }

    /**
     * Ищет наиболее подходящее событие по параметрам пользователя
     *
     * @param array<string, mixed> $params
     */
    public function findEventByParamsWithMaxPriority(array $params): ?Event
    {
        return $this->storage->findEventByParamsWithMaxPriority($params);
    }
}
