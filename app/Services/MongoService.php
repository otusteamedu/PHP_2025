<?php

namespace App\Services;

use MongoDB\Driver\Manager;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\Command;

class MongoService
{
    const MONGO_DB = 'analytics';
    const MONGO_COLLECTION = 'events';

    private $mongo;

    public function __construct()
    {
        $this->mongo = new Manager('mongodb://root:example@mongo:27017');
    }

    public function add($event, $priority, $conditions)
    {
        $document = [
            'event' => $event,
            'priority' => $priority,
            'conditions' => $conditions,
        ];
        $bulk = new BulkWrite;
        $bulk->insert($document);
        $namespace = self::MONGO_DB . '.' . self::MONGO_COLLECTION;
        $result = $this->mongo->executeBulkWrite($namespace, $bulk);

        if ($result->getInsertedCount() == 1) {
            $message = 'Событие добавлено в систему';
        } else {
            $message = 'Ошибка: событие не удалось добавить';
        }

        return $message;
    }

    public function answer($parameters)
    {
        $filter = [];
        foreach ($parameters as $key => $val) {
            $filter["conditions.$key"] = $val;
        }
        $options = [
            'sort' => ['priority' => -1],
            'limit' => 1, 
            'projection' => ['event' => 1]
        ];
        $query = new Query($filter, $options);

        $namespace = self::MONGO_DB . '.' . self::MONGO_COLLECTION;
        $cursor = $this->mongo->executeQuery($namespace, $query);
        $result = current($cursor->toArray());
        $message = !empty($result) ? $result->event : 'Событий нет';

        return $message;
    }

    public function clear()
    {
        $command = new Command(['drop' => self::MONGO_COLLECTION]);
        $this->mongo->executeCommand(self::MONGO_DB, $command);
        return true;
    }

    public function getEvents()
    {
        $query = new Query([]);
        $namespace = self::MONGO_DB . '.' . self::MONGO_COLLECTION;
        $cursor = $this->mongo->executeQuery($namespace, $query);

        $message = '';
        foreach ($cursor as $doc) {
            $data = (array) $doc;
            $arr = ['event' => $data['event'], 'conditions' => $data['conditions'], 'priority' => $data['priority']];
            $message .= json_encode($arr) . PHP_EOL;
        }
        $message = !empty($message) ? $message : 'Нет зарегистрированных событий';

        return $message;
    }
}