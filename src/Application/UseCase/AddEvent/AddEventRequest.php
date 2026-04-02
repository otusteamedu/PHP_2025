<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\AddEvent;

class AddEventRequest
{
    public function __construct(
        public readonly int $priority,
        public readonly array $conditions
    ) {}
}