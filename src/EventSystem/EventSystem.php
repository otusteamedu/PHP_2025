<?php

namespace App\EventSystem;

class EventSystem
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function addEvent($priority, $conditions, $event)
    {
        $eventId = uniqid('event_', true);
        $eventData = json_encode([
            'priority' => $priority,
            'conditions' => $conditions,
            'event' => $event
        ]);

        $this->storage->set("event:$eventId", $eventData);

        foreach ($conditions as $key => $value) {
            $this->storage->sadd("conditions:$key:$value", $eventId);
        }

        echo 'Добавлено событие с ID: ' . $eventId . PHP_EOL;
    }

    public function clearAllEvents()
    {
        $keys = $this->storage->keys('event:*');

        foreach ($keys as $key) {
            $this->storage->del($key);
        }
    }

    public function getBestMatchingEvent($params)
    {
        $bestEvent = null;
        $message = "Событие не найдено." . PHP_EOL;

        $eventKeys = $this->storage->keys("event:*");

        foreach ($eventKeys as $key) {
            $eventData = $this->storage->get($key);

            if ($eventData) {
                $eventData = json_decode($eventData, true);

                $conditionsMet = true;
                foreach ($eventData['conditions'] as $condition => $value) {
                    if (!isset($params[$condition]) || $params[$condition] != $value) {
                        $conditionsMet = false;
                        break;
                    }
                }

                if ($conditionsMet) {
                    if (!$bestEvent || $eventData['priority'] > $bestEvent['priority']) {
                        $bestEvent = $eventData;
                    }
                }
            }
        }

        if ($bestEvent) {
            $message = "Лучшее событие: " . json_encode($bestEvent) . PHP_EOL;
        }

        return $message;
    }
}
