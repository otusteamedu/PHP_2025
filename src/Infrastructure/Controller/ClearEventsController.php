<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Controller;

use Kamalo\EventsService\Application\UseCase\ClearEvents\ClearEventsUseCase;
use Kamalo\EventsService\Application\UseCase\ClearEvents\ClearEventsResponse;

class ClearEventsController
{
    public function __construct(
        private readonly ClearEventsUseCase $useCase
    ) {
    }

    public function __invoke(): void
    {
        try {
            $response = ($this->useCase)();
        } catch (\Throwable $e) {
            $response = [
                'message' => $e->getMessage(),
            ];
        }
        $this->view($response);
    }

    public function view(ClearEventsResponse|array $response): void
    {
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}