<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\GetTaskStatus;

use App\Http\Api\v1\Task\GetTaskStatus\Input\GetTaskStatusDTO;
use App\Infrastructure\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class GetTaskStatusController
{
    public function __construct(
        private GetTaskStatusManager $manager
    ) {}

    #[OA\Get(
        path: '/api/v1/tasks/{request_id}',
        operationId: 'getTaskStatus',
        summary: 'Get task status by request_id',
        tags: ['Tasks']
    )]
    #[OA\Parameter(
        name: 'request_id',
        description: 'Request UUID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 200,
        description: 'Task status',
        content: new OA\JsonContent(ref: '#/components/schemas/TaskStatusResponse')
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid request_id',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
    )]
    #[OA\Response(
        response: 404,
        description: 'Task not found',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
    )]
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $result = $this->manager->get(new GetTaskStatusDTO((string) ($args['request_id'] ?? '')));

        return JsonResponder::respond($response, $result);
    }
}
