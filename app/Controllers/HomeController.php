<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    private $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('redis', 6379);
    }

    public function add(Request $request, Response $response, $args): Response
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $event = (string) $data['event'] ?? '';
        $priority = (int) $data['priority'] ?? 0;
        $conditions = (array) $data['conditions'];
        ksort($conditions);

        if (empty($event) || empty($priority) || empty($conditions)) {
            $response->getBody()->write('Ошибка: невалидное описание события');
            return $response;
        }

        $conditions1 = [];
        foreach ($conditions as $key => $val) {
            $conditions1[] = $key . ':' . $val;
        }
        $conditionStr = implode('::', $conditions1);

        $this->redis->hSet('events', $event, $conditionStr);
        $this->redis->zAdd('priorities', $priority, $event);

        $response->getBody()->write('Событие добавлено в систему');
        return $response;
    }

    public function answer(Request $request, Response $response, $args): Response
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        $parameters = $data['params'] ?? [];

        if (empty($parameters)) {
            $response->getBody()->write('Некорректный запрос');
            return $response;
        }

        ksort($parameters);
        $params = [];
        foreach ($parameters as $par => $val) {
            $params[] = "$par:$val";
        }

        $events = $this->redis->hGetAll('events');
        $rightEvents = [];
        foreach ($events as $ev => $conditions) {
            $condArr = explode('::', $conditions);
            $res = true;
            foreach ($params as $par) {
                if (!in_array($par, $condArr)) {
                    $res = false;
                    break;
                }
            }
            if ($res) {
                $rightEvents[] = $ev;
            }
        }

        if (empty($rightEvents)) {
            $response->getBody()->write('Нет событий');
            return $response;
        }

        $event = '';
        $currentPriority = 0;
        foreach ($rightEvents as $ev) {
            $priority = $this->redis->zScore('priorities', $ev);
            if ($priority > $currentPriority) {
                $currentPriority = $priority;
                $event = $ev;
            }
        }

        $response->getBody()->write($event);
        return $response;
    }

    public function showEventConditions(Request $request, Response $response, $args)
    {
        $events = $this->redis->hGetAll('events');
        $message = !empty($events) ? json_encode($events) : 'Нет зарегистрированных событий';
        $response->getBody()->write($message);
        return $response;
    }

    public function showEventPriorities(Request $request, Response $response, $args)
    {
        $events = $this->redis->zRevRange('priorities', 0, -1, ['WITHSCORES' => true]);
        $message = !empty($events) ? json_encode($events) : 'Нет зарегистрированных событий';
        $response->getBody()->write($message);
        return $response;
    }

    public function clear(Request $request, Response $response, $args)
    {
        $this->redis->del('events');
        $this->redis->del('priorities');
        $response->getBody()->write('Хранилище событий очищено');
        return $response;
    }
}