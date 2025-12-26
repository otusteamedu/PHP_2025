<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Event;
use MongoDB\Client;
use MongoDB\Collection;

class MongoEventRepository implements EventRepositoryInterface
{
    private const string MONGODB_CONNECTION_STRING = 'mongodb://mongodb:27017';

    private const string MONGODB_DB = 'events_db';

    private const string MONGODB_COLLECTION_NAME = 'events';

    private Collection $collection;

    public function __construct()
    {
        $client = new Client(self::MONGODB_CONNECTION_STRING);
        $this->collection = $client->selectDatabase(self::MONGODB_DB)->selectCollection(self::MONGODB_COLLECTION_NAME);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function save(Event $event): bool
    {
        $result = $this->collection->insertOne([
            'priority' => $event->getPriority(),
            'conditions' => $event->getConditions(),
            'event' => $event->getEvent()
        ]);

        $event->setId((string)$result->getInsertedId());

        return $result->getInsertedCount() === 1;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $cursor = $this->collection->find();

        $events = [];
        foreach ($cursor as $document) {
            $events[] = Event::fromArray([
                'id' => (string)$document['_id'],
                'priority' => $document['priority'],
                'conditions' => (array)$document['conditions'],
                'event' => (array)$document['event']
            ]);
        }

        return $events;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->collection->deleteMany([]);

        return true;
    }
}
