<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use OpenApi\Attributes as OA;
use Otus\Queue\Application\UseCase\Chat\Store;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Exception\ValidationException;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

final readonly class StoreController
{
    /**
     * @param Store $useCase
     */
    public function __construct(
        private Store $useCase,
    ) {
    }

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     */
    #[
        OA\Post(
            path: '/api/store',
            summary: 'Store chat message',
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    required: [
                        'author',
                        'text',
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
                    ],
                ),
            ),
            tags: [
                'Chat',
            ],
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Message stored',
                    content: new OA\JsonContent(
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
                new OA\Response(
                    response: 422,
                    description: 'Validation failed',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'errors',
                                type: 'object',
                                example: [
                                    'author' => 'Author is required',
                                ],
                                additionalProperties: new OA\AdditionalProperties(
                                    type: 'string'
                                ),
                            ),
                        ],
                    ),
                ),
            ],
        ),
    ]
    public function __invoke(Request $request): ResponseInterface
    {
        try {
            $message = new Message(
                author: $request->body->get('author', ''),
                text: $request->body->get('text', ''),
                createdAt: time(),
            );

            $this->useCase->handle($message);
        } catch (ValidationException $validationException) {
            return ResponseFactory::toJson(
                [
                    'errors' => $validationException->getErrors(),
                ],
                422,
            );
        }

        return ResponseFactory::toJson(
            body: $message->toArray(),
            statusCode: 201,
        );
    }
}
