<?php

namespace Infrastructure\Repositories;

use Domain\Entity\Event;
use Domain\Repository\EventRepositoryInterface;
use Infrastructure\Data\EventHandbook;
use RuntimeException;

/*
 * Имитация запроса в БД
 */
class EventRepository implements EventRepositoryInterface
{
    public function findAll(): array {
        // TODO: Implement delete() method.

        return EventHandbook::getEvents();
    }

    public function findById(int $id): ?Event {
        // TODO: Implement delete() method.

        $events = EventHandbook::getEvents();

        foreach ($events as $event) {
            if ($event->getId() === $id) {
                break;
            }
        }

        return $event ?? null;
    }

    public function save(Event $event): int {
        // TODO: Implement delete() method.

        sleep(1);

        return rand(50, 1000);
    }

    public function delete(Event $event): void {
        // TODO: Implement delete() method.

        throw new RuntimeException('Not implemented');
    }
}