<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\CreateTask;

use App\Http\Api\v1\Task\CreateTask\Input\CreateTaskDTO;
use App\Infrastructure\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class CreateTaskController
{
    public function __construct(
        private CreateTaskManager $manager
    ) {}

    #[OA\Post(
        path: '/api/v1/tasks',
        operationId: 'createTask',
        summary: 'Create a task for asynchronous processing',
        tags: ['Tasks']
    )]
    #[OA\RequestBody(
        required: false,
        content: new OA\JsonContent(
            type: 'object',
            example: ['email' => 'user@example.com', 'priority' => 'high'],
            additionalProperties: true
        )
    )]
    #[OA\Response(
        response: 202,
        description: 'The task has been queued',
        content: new OA\JsonContent(ref: '#/components/schemas/CreatedTaskResponse')
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid request body',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
    )]
    public function __invoke(Request $request, Response $response): Response
    {
        $dto = $this->manager->create(new CreateTaskDTO($request->getParsedBody()));

        return JsonResponder::respond($response, $dto, 202);
    }
}
