<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Swagger;

use OpenApi\Attributes as OA;
use OpenApi\Generator;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

#[
    OA\OpenApi(
        openapi: '3.1.0',
        info: new OA\Info(
            version: '1.0.0',
            title: 'OTUS',
        ),
    ),
    OA\Components(
        schemas: [],
        responses: [],
        parameters: []
    ),
]
class ApiController
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $generator = new Generator()
            ->generate([
                __DIR__ . '/../',
            ]);

        $generator
            ->servers = [
                new OA\Server('/', 'OTUS'),
            ];

        return ResponseFactory::toRaw(
            body: $generator->toJson(),
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
