<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Controller;

use Kamalo\EventsService\Application\UseCase\GetSuitableEvent\GetSuitableEventUseCase;
use Kamalo\EventsService\Application\UseCase\GetSuitableEvent\GetSuitableEventRequest;
use Kamalo\EventsService\Application\UseCase\GetSuitableEvent\GetSuitableEventResponse;

class GetSuitableEventController
{
    public function __construct(
        private readonly GetSuitableEventUseCase $useCase
    ) {
    }

    public function __invoke(): void
    {
        try {
            $response = ($this->useCase)($this->getRequest());
        } catch (\Throwable $e) {
            $response = [
                'message' => $e->getMessage(),
            ];
        }
        $this->view($response);
    }

    private function view(GetSuitableEventResponse|array $response): void
    {
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    private function getRequest(): GetSuitableEventRequest
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return new GetSuitableEventRequest($data['params']);
    }
}