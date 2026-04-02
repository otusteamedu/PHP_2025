<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\GetSuitableEvent;

use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;
class GetSuitableEventUseCase
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {
    }

    public function __invoke(GetSuitableEventRequest $request): GetSuitableEventResponse
    {
        $event = $this->repository->findByParams($request->params);

        if ($event === null) {
            throw new \Exception('Событие не найдено. Измените параметры запроса');
        }

        
        return new GetSuitableEventResponse(
            $event->getId(),
            $event->getPriority(),
            $event->getConditions()
        );
    }
}