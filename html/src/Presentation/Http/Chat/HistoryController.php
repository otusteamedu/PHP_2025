<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use OpenApi\Attributes as OA;
use Otus\Queue\Application\UseCase\Chat\History;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

final readonly class HistoryController
{
    /**
     * @param History $useCase
     */
    public function __construct(
        private History $useCase,
    ) {
    }

    /**
     * @return ResponseInterface
     */
    #[
        OA\Get(
            path: '/api/history',
            summary: 'Get chat history',
            tags: [
                'Chat',
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Chat history',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(
                            required: [
                                'author',
                                'text',
                                'created_at',
                            ],
                            properties: [
                                new OA\Property(
                                    property: 'author',
                                    type: 'string',
                                    example: 'John'
                                ),
                                new OA\Property(
                                    property: 'text',
                                    type: 'string',
                                    example: 'Hello, world!'
                                ),
                                new OA\Property(
                                    property: 'created_at',
                                    type: 'integer',
                                    example: 1
                                ),
                            ],
                        ),
                    ),
                ),
            ],
        ),
    ]
    public function __invoke(): ResponseInterface
    {
        return ResponseFactory::toJson(
            body: $this->useCase->handle(),
        );
    }
}
