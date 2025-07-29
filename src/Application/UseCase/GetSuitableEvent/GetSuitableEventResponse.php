<?

declare(strict_types=1);

namespace  Kamalo\EventsService\Application\UseCase\GetSuitableEvent;
class GetSuitableEventResponse{
    public function __construct(
        public readonly int $id,
        public readonly int $priority,
        public readonly array $conditions
    ){}
}