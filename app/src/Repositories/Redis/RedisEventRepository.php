<?php

declare(strict_types=1);

namespace App\Repositories\Redis;

use App\Application;
use App\Forms\EventSearch;
use App\Models\Condition;
use App\Models\Event;
use App\Repositories\EventRepositoryInterface;
use App\Storage\Redis\ClientBuilder;
use App\Storage\Redis\Config;
use Redis;
use RedisException;

/**
 * Class RedisEventSearchRepository
 * @package App\Repositories\Redis
 */
class RedisEventRepository implements EventRepositoryInterface
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var Redis
     */
    private Redis $client;

    /**
     * @throws RedisException
     */
    public function __construct()
    {
        $this->config = new Config(Application::$app->getConfig());
        $this->client = ClientBuilder::create($this->config);
    }

    /**
     * @inheritdoc
     * @throws RedisException
     */
    public function create(Event $event): void
    {
        foreach ($event->getConditions() as $condition) {
            $this->client->zAdd(
                "{$condition->getName()}:{$condition->getValue()}",
                $event->getPriority(),
                $event->getId()
            );
        }

        $this->client->set(
            'event:' . $event->getId(),
            json_encode($event->toArray())
        );
    }

    /**
     * @inheritdoc
     * @throws RedisException
     */
    public function search(EventSearch $eventSearch): ?Event
    {
        $setNames = $this->getConditionsSetNames($eventSearch->getConditions());
        $this->client->zInterStore('intersectSet', $setNames, null, 'max');

        $events = $this->client->zPopMax('intersectSet');
        if (empty($events)) {
            return null;
        }

        $eventKey = array_key_first($events);
        $eventData = $this->client->get("event:$eventKey");
        if (!$eventData) {
            return null;
        }

        return Event::createFromJson($eventData);
    }

    /**
     * @param array $conditions
     * @return array
     */
    private function getConditionsSetNames(array $conditions): array
    {
        return array_map(
            function (Condition $condition) {
                return "{$condition->getName()}:{$condition->getValue()}";
            },
            $conditions
        );
    }
}
