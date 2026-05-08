<?php

declare(strict_types=1);

namespace App\Application\Console\Controller;

use App\Application\Command\AddEventCommand;
use App\Application\Console\InputArguments;
use App\Application\Handler\AddEventHandler;
use App\Domain\Service\EventService;

/**
 * Консольный контроллер команды add
 */
final class AddEventController
{
    private AddEventHandler $handler;

    public function __construct(EventService $service)
    {
        $this->handler = new AddEventHandler($service);
    }

    /**
     * Собирает команду добавления события из аргументов и возвращает данные для вывода
     *
     * @return array<string, mixed>
     */
    public function handle(InputArguments $arguments): array
    {
        $event = $this->handler->handle(
            new AddEventCommand(
                $arguments->getRequiredInt('priority'),
                $arguments->getRequiredJsonObject('conditions'),
                $arguments->getRequiredJsonObject('event'),
            ),
        );

        return [
            'status' => 'ok',
            'message' => 'Event added.',
            'event' => $event->toArray(),
        ];
    }
}
