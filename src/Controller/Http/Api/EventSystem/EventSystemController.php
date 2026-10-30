<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\EventSystem;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\HttpStatus;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\EventSystem\EventService;
use App\Domain\EventSystem\Model\AddEventModel;
use App\Domain\EventSystem\Model\SearchEventModel;

class EventSystemController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly EventService $eventService,
    ) {
    }

    public function createEvent(): Response
    {
        $payload = $this->request->getPayload();
        $event = $payload['event'];
        $this->eventService->addEvent(
            new AddEventModel(
                id: $event['id'],
                name: $event['name'],
                priority: $event['priority'],
                conditions: $event['conditions'],
            ),
        );

        return $this->json([
            'success' => true,
            'message' => 'Событие успешно добавлено.',
            'data' => $event,
        ], HttpStatus::Created->value);
    }

    public function getEvent(): Response
    {
        $payload = $this->request->getPayload();
        $params = $payload['params'];
        $eventModel = $this->eventService->getEvent(
            new SearchEventModel($params),
        );

        return $this->json([
            'success' => true,
            'event' => $eventModel->toArray(),
        ], HttpStatus::Ok->value);
    }

    public function deleteEvents(): Response
    {
        $this->eventService->deleteAllEvents();

        return $this->json([
            'success' => true,
            'message' => 'Все события удалены.',
        ], HttpStatus::Ok->value);
    }
}
