<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddEvent;

use App\Application\Command\CommandHandlerInterface;
use App\Domain\Factory\EventFactory;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\EventRepository;

class AddEventCommandHandler implements CommandHandlerInterface
{
    private EventRepositoryInterface $eventRepository;
    private EventFactory $eventFactory;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
        $this->eventFactory = new EventFactory();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(AddEventCommand $command): string
    {
        $event = $this->eventFactory->create($command->priority, $command->name, $command->conditions);
        $this->eventRepository->add($event);

        return $event->getId();
    }

}