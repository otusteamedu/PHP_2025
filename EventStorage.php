<?php

class EventStorage
{
    private Redis $redis;

    /**
     * @throws RedisException
     */
    public function __construct($host = 'localhost', $port = 6379)
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
    }

    /**
     * @throws RedisException
     */
    public function addEvent($priority, $conditions, $event): void
    {
        $eventData = [
            'priority' => $priority,
            'conditions' => $conditions,
            'event' => $event
        ];

        $this->redis->zAdd('events', $priority, json_encode($eventData));
    }

    /**
     * @throws RedisException
     */
    public function clearEvents(): void
    {
        $this->redis->del('events');
    }

    /**
     * @throws RedisException
     */
    public function getBestEvent($params)
    {
        $matchedEvents = [];
        $events = $this->redis->zRevRange('events', 0, -1);

        foreach ($events as $ev) {
            $eventData = json_decode($ev, true);
            if ($this->conditionsMet($eventData['conditions'], $params)) {
                $matchedEvents[] = $eventData;
            }
        }

        return !empty($matchedEvents) ? $matchedEvents[0] : null;
    }

    private function conditionsMet($eventConditions, $params): bool
    {
        foreach ($eventConditions as $key => $value) {
            if (!isset($params[$key]) || $params[$key] != $value) {
                return false;
            }
        }

        return true;
    }
}
