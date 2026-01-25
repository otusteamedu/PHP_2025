<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Repositories;

use Dinargab\Homework11\Model\Event;
use Dinargab\Homework11\Repositories\EventRepositoryInterface;

class EventRedisRepository implements EventRepositoryInterface
{
    public \Redis $redis;

    //Saving Single event in hash usting prefix for name
    private const EVENT_PREFIX = "event:";
    // Saving condition in Set using prefix for Set name 
    private const CONDITION_PREFIX = "condition:";

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Adds an event to Redis storage
     * 
     * @param Event $event The event object to be stored
     * @return bool True if the transaction executed successfully, false otherwise
     */

    public function add(Event $event): bool
    {
        $this->redis->multi();
        $this->redis->hSet(self::EVENT_PREFIX . $event->getName(), 'conditions', json_encode($event->getConditions()), 'priority', $event->getPriority());
        //Adding eventnames to set with param:value name to filter out more events on redis side
        foreach ($event->getConditions() as $param => $value) {
            $this->redis->sAdd(self::CONDITION_PREFIX . $param . ":" . $value, $event->getName());
        }
        return is_array($this->redis->exec());
    }

    /**
     * Deletes all data with set prefixes
     * @return bool true if something was deleted false othervise
     */
    public function deleteAll(): bool
    {
        $keysToDelete = array_merge($this->getKeysToDelete(self::CONDITION_PREFIX), $this->getKeysToDelete(self::EVENT_PREFIX));
        if (!empty($keysToDelete)) {
            $this->redis->del($keysToDelete);
            return true;
        }
        return false;
    }

    private function getKeysToDelete($prefixToScan): array
    {
        $keysToDelete = [];
        $iterator = null; // Initialize the iterator
        while (($scanKeys = $this->redis->scan($iterator, $prefixToScan . '*', 100)) !== FALSE) {
            if ($scanKeys) {
                foreach ($scanKeys as $key) {
                    $keysToDelete[] = $key;
                }
            }
        }
        return $keysToDelete;
    }


    /**
     * Finds the highest priority event that matches all given conditions
     * 
     * @param array $conditions Associative array of conditions to match [param => value]
     * @return Event|null The matching event with highest priority, or null if no match found
     */

    public function findByConditions(array $conditions): ?Event
    {
        $keys = [];
        foreach ($conditions as $key => $value) {
            $keys[] = self::CONDITION_PREFIX . $key . ":" . $value;
        }
        $eventCandidatesKeys = $this->redis->sUnion(...$keys);
        if (empty($eventCandidatesKeys)) {
            return null;
        }

        $this->redis->multi(\Redis::PIPELINE);
        foreach ($eventCandidatesKeys as $event) {
            $this->redis->hGetAll($event);
        }
        $eventCandidatesContent = $this->redis->exec();
        $eventCandidates = array_combine($eventCandidatesKeys, $eventCandidatesContent);

        $bestPriority = -1;
        $bestEvent = null;
        foreach ($eventCandidates as $eventKey => $eventCandidate) {
            $eventConditions = json_decode($eventCandidate['conditions'], true);
            if (count($eventConditions) < count($conditions)) {
                continue;
            }
            $addToResult = true;
            foreach ($conditions as $key => $cond) {
                if (!isset($eventConditions[$key]) || $eventConditions[$key] != $cond) {
                    $addToResult = false;
                    break;
                }
            }

            if ($addToResult && $eventCandidate['priority'] > $bestPriority) {
                $bestPriority = $eventCandidate['priority'];
                $bestEvent = $eventCandidate;
                $bestEvent["name"] = $eventKey;
            }
        }
        return new Event($bestEvent["name"], json_decode($bestEvent["conditions"], true), (int) $bestEvent["priority"]);
    }
}
