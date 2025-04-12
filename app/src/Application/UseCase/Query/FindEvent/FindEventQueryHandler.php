<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindEvent;

use App\Application\DTO\EventDTOTransformer;
use App\Application\Query\QueryHandlerInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\EventRepository;

class FindEventQueryHandler implements QueryHandlerInterface
{
    private EventRepositoryInterface $eventRepository;
    private EventDTOTransformer $eventDTOTransformer;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
        $this->eventDTOTransformer = new EventDTOTransformer();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(FindEventQuery $query): FindEventQueryResult
    {
        $result = $this->eventRepository->findById($query->eventId);
        if (!$result) {
            return new FindEventQueryResult(null);
        }
        $eventDto = $this->eventDTOTransformer->fromEntity($result);

        return new FindEventQueryResult($eventDto);
    }

}