<?php

namespace App;

class EventStorage {
    private $redis;

    public function __construct($redis) {
        $this->redis = $redis;
    }

    public function addEvent($priority, $conditions, $event) {
        $eventData = [
            'priority' => $priority,
            'conditions' => $conditions,
            'event' => $event
        ];
        $this->redis->rpush('events', json_encode($eventData));
    }

    public function clearEvents() {
        $this->redis->del('events');
    }

    public function getBestEvent($params) {
        $events = $this->redis->lrange('events', 0, -1);
        $bestEvent = null;
        $highestPriority = -1;

        foreach ($events as $eventJson) {
            $event = json_decode($eventJson, true);
            if ($this->conditionsMet($event['conditions'], $params) && $event['priority'] > $highestPriority) {
                $highestPriority = $event['priority'];
                $bestEvent = $event;
            }
        }

        return $bestEvent;
    }

    private function conditionsMet($conditions, $params) {
        foreach ($conditions as $key => $value) {
            if (!isset($params[$key]) || $params[$key] != $value) {
                return false;
            }
        }
        return true;
    }
}
