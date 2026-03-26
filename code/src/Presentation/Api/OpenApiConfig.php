<?php

declare(strict_types=1);

namespace Api\Presentation\Api;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    info: new OA\Info(
        title: 'API с использованием очередей',
        version: '1.0.0',
        description: 'Rest API с очередями RabbitMQ для асинхронной обработки запросов'
    ),
    servers: [
        new OA\Server(url: 'http://localhost', description: 'Локальный сервер')
    ],
    tags: [
        new OA\Tag(name: 'Requests', description: 'Управление запросами')
    ],
    components: new OA\Components(
        securitySchemes: [
            'ApiKeyAuth' => new OA\SecurityScheme(
                securityScheme: 'ApiKeyAuth',
                type: 'apiKey',
                in: 'header',
                name: 'X-API-Key',
                description: 'API Key авторизация.'
            )
        ]
    )
)]
final class OpenApiConfig
{
}
