<?php

namespace App\Infrastructure\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Todo API', version: '1.0')]
class TodoControllerAnnotations
{
    #[OA\Get(
        path: '/api/v1/todos',
        tags: ['Todos'],
        description: 'Get all todos for user',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of todos',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'user_id', type: 'integer', example: 123),
                            new OA\Property(property: 'content', type: 'string', example: 'Купить молоко'),
                            new OA\Property(property: 'created_at', type: 'string', example: '2024-01-15 10:30:00')
                        ]
                    )
                )
            )
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        description: 'Get todo by id',
        parameters: [
            new OA\Parameter(
                in: 'path', 
                name: 'id', 
                required: true, 
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Todo details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'user_id', type: 'integer', example: 123),
                        new OA\Property(property: 'content', type: 'string', example: 'Купить молоко'),
                        new OA\Property(property: 'created_at', type: 'string', example: '2024-01-15 10:30:00')
                    ]
                )
            ),
            new OA\Response(
                response: 404, 
                description: 'Todo not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
                    ]
                )
            )
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function show() {}

    #[OA\Post(
        path: '/api/v1/todos',
        tags: ['Todos'],
        description: 'Add todo',
        requestBody: new OA\RequestBody(
            description: 'Todo data',
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Новая задача')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Задание добавлено в очередь'),
                        new OA\Property(property: 'task_id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(
                response: 400, 
                description: 'Input data error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Текст должен содержать не менее 3 символов'),
                    ]
                )
            )
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function store() {}

    #[OA\Patch(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        description: 'Update todo',
        parameters: [
            new OA\Parameter(
                in: 'path', 
                name: 'id', 
                required: true, 
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Todo data',
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Обновленная задача')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Задание добавлено в очередь'),
                        new OA\Property(property: 'task_id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(
                response: 400, 
                description: 'Input data error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Текст должен содержать не менее 3 символов'),
                    ]
                )
            )
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        description: 'Delete todo',
        parameters: [
            new OA\Parameter(
                in: 'path', 
                name: 'id', 
                required: true, 
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Задание добавлено в очередь'),
                        new OA\Property(property: 'task_id', type: 'integer', example: 1)
                    ]
                )
            )
        ],
        security: [
            ['BearerAuth' => []]
        ]
    )]
    public function delete() {}
}