<?php

namespace Arlex2305k\Redis;

class RequestHandler
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function handleRequest(string $method, string $uri): void
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        try {
            switch ($method) {
                case 'POST':
                    if ($uri === '/events') {
                        $this->addEvent($data);
                    } else {
                        $this->sendResponse(['error' => 'API endpoint not found'], 404);
                    }
                    break;

                case 'DELETE':
                    if ($uri === '/events') {
                        $this->clearEvents();
                    } else {
                        $this->sendResponse(['error' => 'API endpoint not found'], 404);
                    }
                    break;

                case 'GET':
                    if ($uri === '/events' && isset($_GET['action']) && $_GET['action'] === 'best_match') {
                        $params = $_GET['params'] ?? $data['params'] ?? [];
                        if (is_string($params)) {
                            $params = json_decode($params, true) ?: [];
                        }
                        $this->getBestMatch($params);
                    } elseif ($uri === '/events') {
                        $this->getAllEvents();
                    } else {
                        $this->sendResponse(['error' => 'API endpoint not found'], 404);
                    }
                    break;

                default:
                    $this->sendResponse(['error' => 'Method is not allowed'], 405);
                    break;
            }
        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function addEvent(?array $data): void
    {
        if (!$data) {
            $this->sendResponse(['error' => 'Invalid JSON data'], 400);
            return;
        }

        try {
            $success = $this->eventService->addEvent($data);
            if ($success) {
                $this->sendResponse(['message' => 'Event added successfully'], 201);
            } else {
                $this->sendResponse(['error' => "Couldn't add event"], 500);
            }
        } catch (\InvalidArgumentException $e) {
            $this->sendResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function clearEvents(): void
    {
        $success = $this->eventService->clearEvents();
        if ($success) {
            $this->sendResponse(['message' => 'All events are cleared'], 200);
        } else {
            $this->sendResponse(['error' => "Couldn't clear events"], 500);
        }
    }

    private function getBestMatch(array $params): void
    {
        $result = $this->eventService->getEventByParams($params);
        if ($result) {
            $this->sendResponse($result['event_data'], 200);
        } else {
            $this->sendResponse(['message' => 'The corresponding event was not found'], 404);
        }
    }

    private function getAllEvents(): void
    {
        $events = $this->eventService->getAllEvents();
        $this->sendResponse($events, 200);
    }

    private function sendResponse(mixed $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
