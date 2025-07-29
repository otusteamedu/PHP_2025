<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\AddEvent;

use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;
use Kamalo\EventsService\Application\Factory\EventFactory;


class AddEventUseCase
{

    public function __construct(
        private EventRepositoryInterface $repository,
        private EventFactory $factory
    ) {
    }

    public function __invoke(AddEventRequest $request): AddEventResponse
    {
        $event = $this->factory->create(
            null,
            $request->priority,
            $request->conditions
        );

        $this->repository->add($event);

        return new AddEventResponse('Событие успешно добавлено');
    }
}