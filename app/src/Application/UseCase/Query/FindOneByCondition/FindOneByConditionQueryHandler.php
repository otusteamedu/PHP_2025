<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindOneByCondition;

use App\Application\DTO\EventDTOTransformer;
use App\Application\Query\QueryHandlerInterface;
use App\Application\UseCase\Query\FindOne\FindOneQueryResult;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\EventRepository;

class FindOneByConditionQueryHandler implements QueryHandlerInterface
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
    public function __invoke(FindOneByConditionQuery $query): FindOneQueryResult
    {
        $result = $this->eventRepository->findByCondition($query->conditions);
        if (!$result) {
            return new FindOneQueryResult(null);
        }
        $eventDto = $this->eventDTOTransformer->fromEntity($result);

        return new FindOneQueryResult($eventDto);
    }

}