<?php

namespace Infrastructure\Data;

use DateTime;
use Domain\Entity\Event;

class EventHandbook
{
    /**
     * @return array
     */
    public static function getEvents(): array {
        $eventTypes = [
            'create',
            'update',
            'notice',
        ];

        $events = [];

        for ($i = 0; $i < 30; $i++) {
            $events[] = new Event(
                $i + 1,
                $eventTypes[rand(0, 2)],
                'Тут написано что-то',
                rand(1, 99),
                'Тут написано что-то',
                new DateTime(),
                new DateTime()
            );
        }

        sleep(1);

        return $events;
    }
}