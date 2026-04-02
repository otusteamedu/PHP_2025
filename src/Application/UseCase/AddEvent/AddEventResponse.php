<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\AddEvent;

class AddEventResponse{
    public function __construct(
        public readonly string $message
    ){}
}