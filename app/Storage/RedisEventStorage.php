<?php

declare(strict_types=1);

namespace App\Storage;

use App\Contracts\EventStorageInterface;
use App\Models\Event;
use Predis\Client;

class RedisEventStorage implements EventStorageInterface
{
    private Client $redis;
    private string $eventsKey = 'events';

    public function __construct()
    {
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = (int) (getenv('REDIS_PORT') ?: 6379);
        
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function addEvent(Event $event): bool
    {
        try {
            $eventJson = json_encode($event->toArray());
            $this->redis->hset($this->eventsKey, $event->getId(), $eventJson);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function clearAllEvents(): bool
    {
        try {
            $this->redis->del($this->eventsKey);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function findBestMatchingEvent(array $params): ?Event
    {
        try {
            $events = $this->getAllEvents();
            $matchingEvents = [];

            foreach ($events as $event) {
                if ($event->matchesParams($params)) {
                    $matchingEvents[] = $event;
                }
            }

            if (empty($matchingEvents)) {
                return null;
            }

            usort($matchingEvents, function (Event $a, Event $b) {
                return $b->getPriority() <=> $a->getPriority();
            });

            return $matchingEvents[0];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllEvents(): array
    {
        try {
            $eventsData = $this->redis->hgetall($this->eventsKey);
            $events = [];

            foreach ($eventsData as $eventJson) {
                $eventArray = json_decode($eventJson, true);
                if ($eventArray) {
                    $events[] = Event::fromArray($eventArray);
                }
            }

            return $events;
        } catch (\Exception $e) {
            return [];
        }
    }
} 