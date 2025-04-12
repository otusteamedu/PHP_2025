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
//        foreach ($event->getConditions() as $param => $condition) {
//            var_dump($param, $condition);
//            die;
//        }
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
        if (count(array_unique($result)) > 1) {
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
}