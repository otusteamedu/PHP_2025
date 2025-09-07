<?php

namespace App\Infrastructure\OpenApi;

use OpenApi\Attributes as OA;

class TaskControllerAnnotations
{
    #[OA\Get(
        path: '/api/v1/tasks/{id}',
        tags: ['Check'],
        description: 'Check task status by ID',
        parameters: [
            new OA\Parameter(
                in: 'path', 
                name: 'id', 
                required: true, 
                description: 'Task ID',
                schema: new OA\Schema(type: 'integer', example: 123)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task status information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'task_id', type: 'integer', example: 123),
                        new OA\Property(property: 'status', type: 'string', example: 'completed')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Not Found'),
                    ]
                )
            ),
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function check() {}
}