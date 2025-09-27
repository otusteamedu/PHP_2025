<?php

declare(strict_types=1);

namespace App;

use App\EventService\EventRedisService;
use App\EventService\EventServiceInterface;
use Exception;
use Throwable;

class App
{
    public function handleAddEvent(): void
    {
        try {
            $decodedBody = (new RequestBodyService())->getDecodedJsonBody();

            $eventNameList = $decodedBody['event'] ?? null;
            $priority = $decodedBody['priority'] ?? null;

            if (empty($eventNameList) || empty($priority)) {
                $this->handleResponse('Invalid request', 400);

                return;
            }

            $eventService = $this->getEventService();

            foreach ($eventNameList as $eventName) {
                $eventService->addJsonEvent($eventName, $decodedBody, $priority);
            }

            $this->handleResponse('OK');
        } catch (Throwable) {
            $this->handleResponse('Invalid request', 400);
        }
    }

    public function handleDeleteAllEvents(): void
    {
        try {
            $eventService = $this->getEventService();
            $eventService->clearAllEvents();

            $this->handleResponse('OK');
        } catch (Throwable) {
            $this->handleResponse('Invalid request', 400);
        }
    }

    public function handleGetEvent(): void
    {
        try {
            $decodedBody = (new RequestBodyService())->getDecodedJsonBody();

            $params = $decodedBody['params'] ?? null;

            if (empty($params)) {
                $this->handleResponse('Invalid request', 400);

                return;
            }

            $eventName = 'event';

            $eventService = $this->getEventService();
            $eventList = $eventService->getEventListByEventName($eventName);

            foreach ($eventList as $event) {
                $decodedEvent = json_decode($event, true, 512, JSON_THROW_ON_ERROR);
                $conditions = $decodedEvent['conditions'];

                foreach ($conditions as $key => $value) {
                    if (!isset($params[$key]) || $params[$key] !== $value) {
                        continue 2;
                    }
                }

                $this->handleResponse($event);

                return;
            }

            $this->handleResponse('Event not found');
        } catch (Throwable) {
            $this->handleResponse('Invalid request', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): void {
        http_response_code($statusCode);
        echo $message;
    }

    /**
     * @throws Exception
     */
    private function getEventService(): EventServiceInterface
    {
        if (getenv('EVENT_SERVICE') === 'redis') {
            return new EventRedisService();
        }

        throw new Exception('Invalid event service');
    }
}