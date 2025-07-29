<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Controller;

use Kamalo\EventsService\Application\UseCase\AddEvent\AddEventRequest;
use Kamalo\EventsService\Application\UseCase\AddEvent\AddEventResponse;
use Kamalo\EventsService\Application\UseCase\AddEvent\AddEventUseCase;



class AddEventController
{
    public function __construct(
        private AddEventUseCase $useCase
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

    private function view(AddEventResponse|array $response): void
    {
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    private function getRequest(): AddEventRequest
    {
        $data = json_decode(file_get_contents('php://input'), true);

        return new AddEventRequest(
            $data['priority'],
            $data['conditions']
        );
    }
}