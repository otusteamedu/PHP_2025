<?php declare(strict_types=1);

namespace EManager\Storage;

use MongoDB\Client;

class MongoStorage implements StorageInterface
{
    private \MongoDB\Collection $collection;

    public function __construct(string $uri = 'mongodb://root:example@mongodb:27017', string $dbName = 'event_system', string $collectionName = 'events')
    {
        $client = new Client($uri);
        $this->collection = $client->$dbName->$collectionName;
    }

    public function addEvent(array $event): void
    {
        $this->collection->insertOne($event);
    }

    public function clearEvents(): void
    {
        $this->collection->deleteMany([]);
    }

    public function findMatchingEvent(array $matching): ?array
    {
        if (!isset($matching["params"]) || !is_array($matching['params']))
        {
            return null;
        }

        // Ищем все события, где все условия совпадают
        $query = [];
        foreach ($matching["params"] as $key => $value) {
            $query["conditions.$key"] = $value;
        }

        // Сортируем по приоритету (по убыванию) и берем первое совпадение
        $event = $this->collection->findOne(
            $query,
            ['sort' => ['priority' => -1]]
        );

        return $event ? (array)$event : null;
    }
}