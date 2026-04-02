<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\UseCase\GetSuitableEvent;

class GetSuitableEventRequest
{
    public function __construct(
        public readonly array $params
    ){}
}