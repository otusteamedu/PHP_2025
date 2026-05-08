<?php

declare(strict_types=1);

namespace App\Application\Console\Controller;

use App\Application\Command\FindEventCommand;
use App\Application\Console\InputArguments;
use App\Application\Handler\FindEventHandler;
use App\Domain\Service\EventService;

/**
 * Консольный контроллер команды find
 */
final class FindEventController
{
    private FindEventHandler $handler;

    public function __construct(EventService $service)
    {
        $this->handler = new FindEventHandler($service);
    }

    /**
     * Собирает команду поиска из аргументов и возвращает найденное событие для вывода
     *
     * @return array<string, mixed>
     */
    public function handle(InputArguments $arguments): array
    {
        $event = $this->handler->handle(
            new FindEventCommand(
                $arguments->getRequiredJsonObject('params'),
            ),
        );

        return [
            'status' => 'ok',
            'event' => $event?->toArray(),
        ];
    }
}
