<?php

declare(strict_types=1);

namespace App;

use App\EventService\EventRedisService;
use App\EventService\EventServiceInterface;
use App\Exception\CustomException;
use Exception;
use Throwable;

class App
{
    public function run(): string
    {
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            if ($method === 'POST') {
                if ($_SERVER['REQUEST_URI'] === '/clear') {
                    $this->handleDeleteAllEvents();

                    return $this->handleResponse('Success');
                }

                $this->handleAddEvent();

                return $this->handleResponse('Success');
            }

            if ($method === 'GET') {
                $event = $this->handleGetEvent();

                return $this->handleResponse($event);
            }
        } catch (CustomException $e) {
            return $this->handleResponse($e->getMessage(), $e->getCode());
        } catch (Throwable) {
            return $this->handleResponse('Internal server error', 500);
        }

        return $this->handleResponse('Invalid request method', 400);
    }

    /**
     * @throws CustomException
     */
    public function handleAddEvent(): void
    {
        try {
            $decodedBody = (new RequestBodyService())->getDecodedJsonBody();

            $eventNameList = $decodedBody['event'] ?? null;
            $priority = $decodedBody['priority'] ?? null;

            if (empty($eventNameList) || empty($priority)) {
                throw new CustomException('Invalid request', 400);
            }

            $eventService = $this->getEventService();

            foreach ($eventNameList as $eventName) {
                $eventService->addJsonEvent($eventName, $decodedBody, $priority);
            }
        } catch (CustomException $e) {
            throw $e;
        } catch (Throwable) {
            throw new CustomException('Invalid request', 400);
        }
    }

    /**
     * @throws CustomException
     */
    public function handleDeleteAllEvents(): void
    {
        try {
            $eventService = $this->getEventService();
            $eventService->clearAllEvents();
        } catch (Throwable) {
            throw new CustomException('Invalid request', 400);
        }
    }

    /**
     * @throws CustomException
     */
    public function handleGetEvent(): string
    {
        try {
            $decodedBody = (new RequestBodyService())->getDecodedJsonBody();

            $params = $decodedBody['params'] ?? null;

            if (empty($params)) {
                throw new CustomException('Invalid request', 400);
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

                return $event;
            }

            throw new CustomException('Event not found');
        } catch (CustomException $e) {
            throw $e;
        } catch (Throwable) {
            throw new CustomException('Invalid request', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): string {
        http_response_code($statusCode);

        return $message;
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