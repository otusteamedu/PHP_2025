<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindOne;

use App\Application\DTO\EventDTOTransformer;
use App\Application\Query\QueryHandlerInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\EventRepository;

class FindOneQueryHandler implements QueryHandlerInterface
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
    public function __invoke(FindOneQuery $query): FindOneQueryResult
    {
        $result = $this->eventRepository->findById($query->eventId);
        if (!$result) {
            return new FindOneQueryResult(null);
        }
        $eventDto = $this->eventDTOTransformer->fromEntity($result);

        return new FindOneQueryResult($eventDto);
    }

}