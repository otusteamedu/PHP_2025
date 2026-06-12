<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\EventSystem;

use App\Core\Http\Controller\Base\AbstractController;
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
        return $this->handleOperation(
            operation: function() {
                $event = $this->request->getPayload()['event'];
                $this->eventService->addEvent(
                    new AddEventModel(
                        id: $event['id'],
                        name: $event['name'],
                        priority: $event['priority'],
                        conditions: $event['conditions'],
                    ),
                );

                return ['message' => 'Событие успешно добавлено.'];
            },
        );
    }

    public function getEvent(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $params = $this->request->getPayload()['params'];
                $eventModel = $this->eventService->getEvent(new SearchEventModel($params));

                return ['event' => $eventModel->toArray()];
            },
        );
    }

    public function deleteEvents(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $this->eventService->deleteAllEvents();

                return ['message' => 'Все события удалены.'];
            },
        );
    }
}
