<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Event;
use Predis\Client;

class RedisEventRepository implements EventRepositoryInterface
{
    private const string REDIS_CONNECTION_SCHEME = 'tcp';

    private const string REDIS_EVENTS_HOST = 'redis';

    private const int REDIS_EVENTS_PORT = 6379;

    private const string REDIS_EVENTS_KEY = 'events';

    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client([
            'scheme' => self::REDIS_CONNECTION_SCHEME,
            'host' => self::REDIS_EVENTS_HOST,
            'port' => self::REDIS_EVENTS_PORT,
        ]);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function save(Event $event): bool
    {
        $id = uniqid('event_', true);
        $event->setId($id);

        $data = json_encode($event->toArray());
        $this->redisClient->hset(self::REDIS_EVENTS_KEY, $id, $data);

        return true;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $data = $this->redisClient->hgetall(self::REDIS_EVENTS_KEY);

        $events = [];
        foreach ($data as $eventData) {
            $eventArray = json_decode($eventData, true);
            $events[] = Event::fromArray($eventArray);
        }

        return $events;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->redisClient->del([self::REDIS_EVENTS_KEY]);

        return true;
    }
}
