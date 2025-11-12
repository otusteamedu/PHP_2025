<?php

namespace App\Controller;

use App\Dto\CreateEventEntryDto;
use App\Service\EventService;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/event',
    description: 'Create event',
)]
class CreateEvent
{
    public function __construct(
        private EventService $eventService,
    ) {
    }

    #[OA\Post(
        path: '/event',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "message", type: "string"),
                ],
                type: "object",
            )
        ),
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
    public function process(CreateEventEntryDto $entryDto): string
    {
        $eventData = [
            'message' => $entryDto->message,
        ];

        return $this->eventService->addEvent($eventData);
    }
}
