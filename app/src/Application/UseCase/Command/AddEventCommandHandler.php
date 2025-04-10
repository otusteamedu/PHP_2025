<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command;

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

    public function __invoke(AddEventCommand $command): void
    {
        $event = $this->eventFactory->create($command->priority, $command->name, $command->conditions);
        $result = $this->eventRepository->add($event);
        if ($result['errors']) {
            throw new \Exception('insert failed');
        }
    }

}