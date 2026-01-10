<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\RemoveEvent;

use App\Application\Command\CommandHandlerInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\EventRepository;

class RemoveEventCommandHandler implements CommandHandlerInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(RemoveEventCommand $command): void
    {
        $event = $this->eventRepository->findById($command->eventId);
        if (null !== $event) {
            $this->eventRepository->remove($event);
        }
    }
}