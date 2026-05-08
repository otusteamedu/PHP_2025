<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\AddEventCommand;
use App\Domain\Event\Event;
use App\Domain\Event\EventConditions;
use App\Domain\Service\EventService;

/**
 * Обработчик команды добавления события
 */
final class AddEventHandler
{
    public function __construct(private readonly EventService $eventService)
    {
    }

    /**
     * Создает доменное событие и передает его в сервис событий
     */
    public function handle(AddEventCommand $command): Event
    {
        $event = new Event(
            $command->priority(),
            new EventConditions($command->conditions()),
            $command->eventPayload(),
        );

        $this->eventService->add($event);

        return $event;
    }
}
