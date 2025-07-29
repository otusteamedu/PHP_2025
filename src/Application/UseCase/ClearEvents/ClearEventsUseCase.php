<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\ClearEvents;

use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;

class ClearEventsUseCase
{

    public function __construct(
        private EventRepositoryInterface $repository
    ) {
    }

    public function __invoke(): ClearEventsResponse
    {
        $this->repository->clear();
        return new ClearEventsResponse('События успешно очищены');
    }
}