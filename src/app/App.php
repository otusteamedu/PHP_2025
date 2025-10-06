<?php
declare(strict_types=1);

namespace App;

use App\Storage\RedisEventRepository;
use Redis;

class App
{
    public function run(): string
    {
        header('Content-Type: application/json');

        try {
            $redis = new Redis();
            $redis->connect('redis');

            $repository = new RedisEventRepository($redis);
            $eventService = new EventService($repository);

            $method = $_SERVER['REQUEST_METHOD'];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            switch ($method) {
                case 'POST':
                    if ($path === '/events') {
                        $input = json_decode(file_get_contents('php://input'), true);

                        if (!isset($input['priority'], $input['conditions'], $input['event'])) {
                            http_response_code(400);
                            return json_encode(['error' => 'Invalid input data']);
                        }

                        $eventService->addEvent($input['priority'], $input['conditions'], $input['event']);
                        return json_encode(['status' => 'success']);
                    }

                    if ($path === '/query') {
                        $input = json_decode(file_get_contents('php://input'), true);

                        if (!isset($input['params'])) {
                            http_response_code(400);
                            return json_encode(['error' => 'Params required']);
                        }

                        $event = $eventService->findBestEvent($input['params']);
                        return json_encode(['event' => $event]);
                    }
                    break;

                case 'GET':
                    if ($path === '/events') {
                        return json_encode(['events' => $eventService->getAllEvents()]);
                    }
                    break;

                case 'DELETE':
                    if ($path === '/events') {
                        $eventService->clearEvents();
                        return json_encode(['status' => 'cleared']);
                    }
                    break;

                default:
                    http_response_code(405);
                    return json_encode(['error' => 'Method not allowed']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}