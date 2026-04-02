<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\ClearEvents;

class ClearEventsResponse
{
    public function __construct(
        public readonly string $message
    ) {
    }
}