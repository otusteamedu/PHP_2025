<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Storage\EventsStorageInterface;

class EventsController
{
    private Request $request;
    private Response $response;
    private EventsStorageInterface $storage;

    public function __construct(Request $request, Response $response, EventsStorageInterface $storage)
    {
        $this->request = $request;
        $this->response = $response;
        $this->storage = $storage;
    }

    public function addEvent(): string
    {
        $body = $this->request->getBody();

        $validationError = $this->validateEventBody($body);
        if ($validationError !== null) {
            return $this->response->error(400, $validationError);
        }

        $event = [
            'priority' => (int)$body['priority'],
            'conditions' => $body['conditions'],
            'event' => $body['event'],
        ];

        $this->storage->addEvent($event);

        return $this->response->success(['message' => 'Событие сохранено'], 201);
    }

    public function clearEvents(): string
    {
        $this->storage->clear();

        return $this->response->success(['message' => 'Все события удалены']);
    }

    public function matchEvent(): string
    {
        $body = $this->request->getBody();
        $params = $body['params'] ?? null;

        if (!is_array($params) || $params === []) {
            return $this->response->error(400, 'Необходимо передать объект params с критериями запроса.');
        }

        $matches = $this->storage->findMatches($params);

        if ($matches === []) {
            return $this->response->error(404, 'Подходящее событие не найдено.');
        }

        return $this->response->success($matches);
    }

    private function validateEventBody(array $body): ?string
    {
        if (!isset($body['priority'])) {
            return 'Поле priority обязательно.';
        }

        if (!is_numeric($body['priority'])) {
            return 'priority должен быть числом.';
        }

        if (!isset($body['conditions']) || !is_array($body['conditions']) || $body['conditions'] === []) {
            return 'conditions должен быть непустым объектом условий.';
        }

        if (!isset($body['event'])) {
            return 'Поле event обязательно.';
        }

        return null;
    }
}
