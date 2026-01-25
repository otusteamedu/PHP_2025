<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Repositories;

use Dinargab\Homework11\Model\Event;
use Dinargab\Homework11\Repositories\EventRepositoryInterface;
use MongoDB\Client;
use MongoDB\Collection;

class EventMongoRepository implements EventRepositoryInterface
{
    private Collection $collection;
    private const DATABASE_NAME = 'events_db';
    private const COLLECTION_NAME = 'events';

    public function __construct(Client $mongoClient)
    {
        $this->collection = $mongoClient->selectDatabase(self::DATABASE_NAME)
            ->selectCollection(self::COLLECTION_NAME);
    }


    /**
     * Deletes all events from the MongoDB collection
     *
     * @return bool True if any documents were deleted, false if no documents were found
     */
    public function add(Event $event): bool
    {
        $document = [
            'name' => $event->getName(),
            'conditions' => $event->getConditions(),
            'priority' => $event->getPriority(),
            'conditions_count' => count($event->getConditions()), // Store count for optimization
        ];

        $result = $this->collection->insertOne($document);

        return $result->getInsertedCount() === 1;
    }

    /**
     * Finds the highest priority event that matches all given conditions
     *     *
     * @param array $conditions Associative array of conditions to match [key => value]
     * @return Event|null The matching event with highest priority, or null if no match found
     */
    public function deleteAll(): bool
    {
        $result = $this->collection->deleteMany([]);
        return $result->getDeletedCount() > 0;
    }

    public function findByConditions(array $conditions): ?Event
    {
        // Get all events that could potentially match (events that have at least one condition matching)
        $potentialEvents = $this->findPotentialEvents($conditions);

        if (empty($potentialEvents)) {
            return null;
        }

        // Filter events where ALL event conditions are satisfied by the search conditions
        $matchingEvents = $this->filterFullyMatchingEvents($potentialEvents, $conditions);

        if (empty($matchingEvents)) {
            return null;
        }

        // Find the event with highest priority
        $bestEvent = $this->findHighestPriorityEvent($matchingEvents);

        return new Event(
            $bestEvent['name'],
            (array) $bestEvent['conditions'],
            (int) $bestEvent['priority']
        );
    }


    /**
     * Finds potential events that match at least one of the search conditions
     *
     * @param array $conditions Associative array of search conditions [key => value]
     * @return array Array of MongoDB documents representing potential matching events
     */

    private function findPotentialEvents(array $conditions): array
    {
        $orConditions = [];
        foreach ($conditions as $key => $value) {
            $orConditions[] = ["conditions.{$key}" => $value];
        }

        $query = [];
        if (!empty($orConditions)) {
            $query['$or'] = $orConditions;
        }

        $query['conditions_count'] = ['$lte' => count($conditions)];

        return $this->collection->find($query)->toArray();
    }

    /**
     * Filters potential events to find those that match ALL search conditions exactly
     *
     * @param array $potentialEvents Array of potential event documents from MongoDB
     * @param array $searchConditions Associative array of search conditions [key => value]
     * @return array Array of events that fully match all search conditions
     */

    private function filterFullyMatchingEvents(array $potentialEvents, array $searchConditions): array
    {
        $matchingEvents = [];

        foreach ($potentialEvents as $event) {
            $eventConditions = (array) $event['conditions'];
            $allConditionsMatch = true;

            foreach ($eventConditions as $eventKey => $eventValue) {
                if (!isset($searchConditions[$eventKey]) || $searchConditions[$eventKey] != $eventValue) {
                    $allConditionsMatch = false;
                    break;
                }
            }

            if ($allConditionsMatch) {
                $matchingEvents[] = $event;
            }
        }

        return $matchingEvents;
    }

    /**
     * Find the event with the highest priority among matching events
     * 
     * @param array $matchingEvents Array of matching event documents
     * @return array The event document with the highest priority
     */
    private function findHighestPriorityEvent(array $matchingEvents): array
    {
        $bestEvent = $matchingEvents[0];
        $bestPriority = (int) $bestEvent['priority'];

        foreach ($matchingEvents as $event) {
            $currentPriority = (int) $event['priority'];
            if ($currentPriority > $bestPriority) {
                $bestPriority = $currentPriority;
                $bestEvent = $event;
            }
        }
        return $bestEvent->getArrayCopy();
    }
}
