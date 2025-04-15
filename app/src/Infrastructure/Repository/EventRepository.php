<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\App;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Mapper\EventMapper;
use Predis\Client;

class EventRepository implements EventRepositoryInterface
{
    private const string EVENT_PREFIX = 'event:';
    private const string CONDITION_PREFIX = 'condition:';
    private Client $client;
    private EventMapper $eventMapper;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => App::$app->getConfigValue('scheme'),
            'host' => App::$app->getConfigValue('host'),
            'port' => App::$app->getConfigValue('port'),
        ]);
        $this->eventMapper = new EventMapper();

    }

    public function add(Event $event): void
    {
        $this->client->multi();
        foreach ($event->getConditions() as $param => $condition) {
            $this->client->zadd(self::CONDITION_PREFIX . $param . ':' . $condition, [$event->getId() => $event->getPriority()]);
        }
        $key = self::EVENT_PREFIX . $event->getId();
        foreach ($event->jsonSerialize() as $name => $value) {
            if ($name === 'conditions') {
                $this->client->hset($key, $name, json_encode($value));
                continue;
            }
            $this->client->hset($key, $name, $value);
        }
        $result = $this->client->exec();
        if (!is_array($result)) {
            throw new \Exception('Db record failure.');
        }


        if (count(array_unique($result)) > 1 && current(array_unique($result)) === 0) {
            throw new \Exception('Db record failure.');

        }
    }

    public function findById(string $eventId): ?Event
    {
        $result = $this->client->hgetall(self::EVENT_PREFIX . $eventId);
        if (empty($result)) {
            return null;
        }

        return $this->eventMapper->map($result);
    }

    public function remove(Event $event): void
    {
        $this->client->hdel(self::EVENT_PREFIX . $event->getId(), array_keys($event->jsonSerialize()));
    }

    public function findByCondition(array $conditions): ?Event
    {
        $zKeys = [];
        $event = null;
        $exist = [];

        foreach ($conditions as $param => $condition) {
            $key = self::CONDITION_PREFIX . $param . ':' . $condition;
            foreach ($this->client->zrange($key, 0, -1, ["WITHSCORES" => true]) as $eventId => $priority) {
                $exist[$eventId] = $priority;
            }
            if (!empty($exist)) {
                $zKeys[] = $key;
            }
        }

        if (!empty($zKeys)) {
            if (count($zKeys) > 1) {
                $inter = $this->client->zinter($zKeys, [], 'max', true);
                arsort($inter);
                if (!empty($inter)) {
                    $result = $this->client->hgetall(self::EVENT_PREFIX . current(array_keys($inter)));
                }
            }
            if (count($zKeys) === 1 && count($conditions) === 1) {
                arsort($exist);
                $result = $this->client->hgetall(self::EVENT_PREFIX . current(array_keys($exist)));
            }
            if (isset($result)) {
                $event = $this->eventMapper->map($result);
            }
        }

        return $event;
    }
}