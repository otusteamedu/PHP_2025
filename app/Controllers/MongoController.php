<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MongoController
{
    private $mongo;

    public function __construct()
    {
        $this->mongo = new \MongoDB\Driver\Manager('mongodb://root:example@mongo:27017');
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

        $document = [
            'event' => $event,
            'priority' => $priority,
            'conditions' => $conditions,
        ];
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->insert($document);
        $namespace = 'analytics.events';
        $result = $this->mongo->executeBulkWrite($namespace, $bulk);

        if ($result->getInsertedCount() == 1) {
            $response->getBody()->write('Событие добавлено в систему');
        } else {
            $response->getBody()->write('Ошибка: событие не удалось добавить');
        }

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

        $filter = [];
        foreach ($parameters as $key => $val) {
            $filter["conditions.$key"] = $val;
        }
        $options = [
            'sort' => ['priority' => -1],
            'limit' => 1, 
            'projection' => ['event' => 1]
        ];
        $query = new \MongoDB\Driver\Query($filter, $options);

        $namespace = 'analytics.events';
        $cursor = $this->mongo->executeQuery($namespace, $query);
        $result = current($cursor->toArray());
        $message = !empty($result) ? $result->event : 'Событий нет';

        $response->getBody()->write($message);
        return $response;
    }

    public function showEventConditions(Request $request, Response $response, $args)
    {
        $query = new \MongoDB\Driver\Query([]);
        $namespace = 'analytics.events';
        $cursor = $this->mongo->executeQuery($namespace, $query);

        $message = '';
        foreach ($cursor as $doc) {
            $message .= print_r($doc, true) . PHP_EOL;
        }

        $message = !empty($message) ? $message : 'Нет зарегистрированных событий';
        $response->getBody()->write($message);
        return $response;
    }

    public function showEventPriorities(Request $request, Response $response, $args)
    {}

    public function clear(Request $request, Response $response, $args)
    {
        $db = 'analytics';
        $collection = 'events';
        $command = new \MongoDB\Driver\Command(['drop' => $collection]);
        $this->mongo->executeCommand($db, $command);

        $response->getBody()->write('Хранилище событий очищено');
        return $response;
    }
}