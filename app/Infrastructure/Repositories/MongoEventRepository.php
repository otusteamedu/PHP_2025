<?php

namespace App\Infrastructure\Repositories;

use App\Application\EventRepositoryInterface;
use App\Domain\Entities\Event;
use App\Domain\ValueObjects\Conditions;
use App\Domain\ValueObjects\EventName;
use App\Domain\ValueObjects\Priority;
use MongoDB\Driver\Manager;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\Command;

class MongoEventRepository implements EventRepositoryInterface
{
    const MONGO_DB = 'analytics';
    const MONGO_COLLECTION = 'events';

    private $mongo;

    public function __construct()
    {
        $user = getenv('MONGO_USERNAME');
        $password = getenv('MONGO_PASSWORD');
        $this->mongo = new Manager("mongodb://$user:$password@mongo:27017");
    }

    public function save(Event $event): void
    {
        $document = [
            'event' => $event->getEventName()->toString(),
            'priority' => $event->getPriority()->toInt(),
            'conditions' => $event->getConditions()->toArray(),
        ];
        $bulk = new BulkWrite;
        $bulk->insert($document);
        $namespace = self::MONGO_DB . '.' . self::MONGO_COLLECTION;
        $this->mongo->executeBulkWrite($namespace, $bulk);
    }

    public function fetchAll(): array
    {
        $query = new Query([], []);
        $namespace = self::MONGO_DB . '.' . self::MONGO_COLLECTION;
        $cursor = $this->mongo->executeQuery($namespace, $query);
        $results = $cursor->toArray();

        $events = [];
        foreach ($results as $res) {
            $events[] = new Event(
                new EventName($res->event),
                new Priority($res->priority),
                new Conditions((array) $res->conditions)
            );
        }

        return $events;
    }

    public function deleteAll(): void
    {
        $command = new Command(['drop' => self::MONGO_COLLECTION]);
        $this->mongo->executeCommand(self::MONGO_DB, $command);
    }
}