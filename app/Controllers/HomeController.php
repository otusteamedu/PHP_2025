<?php
namespace App\Controllers;

use App\Services\MongoService;
use App\Services\RedisService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    private $service;

    public function __construct()
    {
        $driver = getenv('DRIVER');
        $this->service = $driver == 'redis' ? new RedisService() : new MongoService();
    }

    public function add(Request $request, Response $response, $args)
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $event = (string) $data['event'] ?? '';
        $priority = (int) $data['priority'] ?? 0;
        $conditions = (array) $data['conditions'] ?? [];
        ksort($conditions);

        if (empty($event) || empty($priority) || empty($conditions)) {
            $response->getBody()->write('Ошибка: невалидное описание события');
            return $response;
        }

        $result = $this->service->add($event, $priority, $conditions);
        
        $response->getBody()->write($result);
        return $response;
    }

    public function answer(Request $request, Response $response, $args)
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        $parameters = $data['params'] ?? [];

        if (empty($parameters)) {
            $response->getBody()->write('Некорректный запрос');
            return $response;
        }
        ksort($parameters);

        $message = $this->service->answer($parameters);

        $response->getBody()->write($message);
        return $response;
    }

    public function events(Request $request, Response $response, $args)
    {
        $message = $this->service->getEvents();
        $response->getBody()->write($message);
        return $response;
    }

    public function clear(Request $request, Response $response, $args)
    {
        $result = $this->service->clear();
        $message = $result ? 'Хранилище событий очищено' : 'Не удалось очистить хранилище';
        $response->getBody()->write($message);
        return $response;
    }
}