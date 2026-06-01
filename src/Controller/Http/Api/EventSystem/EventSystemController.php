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

    public function addEvent(): Response
    {
        try {
            $event = $this->request->getPayload()['event'];
            $this->eventService->addEvent(
                new AddEventModel(
                    id: $event['id'],
                    name: $event['name'],
                    priority: $event['priority'],
                    conditions: $event['conditions'],
                ),
            );
            $data['success'] = true;
            $data['message'] = 'Событие успешно добавлено.';
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return $this->json($data, $httpCode);
    }

    public function getEvent(): Response
    {
        try {
            $params = $this->request->getPayload()['params'];
            $eventModel = $this->eventService->getEvent(new SearchEventModel($params));
            $data['success'] = true;
            $data['event'] = $eventModel->toArray();
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return $this->json($data, $httpCode);
    }

    public function deleteEvents(): Response
    {
        try {
            $this->eventService->deleteAllEvents();
            $data['success'] = true;
            $data['message'] = 'Все события удалены.';
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return $this->json($data, $httpCode);
    }
}
