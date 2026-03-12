<?php

declare(strict_types=1);

namespace Queues\Presentation\Controllers;

use Queues\Application\DTO\StatementRequestDTO;
use Queues\Application\UseCases\CreateStatementUseCase;
use Queues\Application\Interfaces\RequestInterface;
use Queues\Application\Interfaces\ResponseInterface;
use Queues\Presentation\Web\Router;

class StatementsController
{
    public function __construct(
        private readonly CreateStatementUseCase $createStatementUseCase,
        private readonly string $templatePath
    ) {
    }

    public function registerRoutes(Router $router): void
    {
        $router->get('/', [$this, 'showForm']);
        $router->post('/', [$this, 'create']);
    }

    public function showForm(RequestInterface $request, ResponseInterface $response): void
    {
        $response->html($this->templatePath . '/statement-form.php');
    }

    public function create(RequestInterface $request, ResponseInterface $response): void
    {
        $result = $this->createStatementUseCase->execute(
            new StatementRequestDTO(
                $request->post('dateFrom', ''),
                $request->post('dateTo', ''),
                $request->post('email', '')
            )
        );

        $response->json([
            'success' => true,
            'data' => [
                'id' => $result->id,
                'status' => $result->status->value,
                'message' => $result->message,
            ]
        ]);
    }
}
