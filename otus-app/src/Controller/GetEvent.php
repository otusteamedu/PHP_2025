<?php

namespace App\Controller;

use App\Service\EventService;
use JsonException;
use OpenApi\Attributes as OA;

#[
    OA\PathItem(
        path: '/event/{id}',
        description: 'Get event',
    ),
    OA\Info(
        version: '1.0',
        title: 'Get event',
    )
]

class GetEvent
{
    public function __construct(
        private EventService $eventService,
    ) {
    }

    #[OA\Get(
        path: '/event/{id}',
        parameters: [
            new OA\Parameter(name: "id", in: "query", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "string")),
                    ]
                )
            )
        ]
    )]
    /**
     * @throws JsonException
     */
    public function process(string $eventId): array
    {
        return $this->eventService->getEvent($eventId);
    }
}
