<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\FindEventCommand;
use App\Domain\Event\Event;
use App\Domain\Service\EventService;

/**
 * Обработчик команды поиска наиболее подходящего события
 */
final class FindEventHandler
{
    public function __construct(private readonly EventService $eventService)
    {
    }

    /**
     * Возвращает событие с максимальным приоритетом для переданных параметров
     */
    public function handle(FindEventCommand $command): ?Event
    {
        return $this->eventService->findEventByParamsWithMaxPriority($command->params());
    }
}
