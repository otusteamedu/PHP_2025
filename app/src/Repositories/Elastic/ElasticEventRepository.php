<?php

declare(strict_types=1);

namespace App\Repositories\Elastic;

use App\Application;
use App\Forms\EventSearch;
use App\Models\Event;
use App\Repositories\Elastic\Queries\EventQuery;
use App\Repositories\EventRepositoryInterface;
use App\Storage\Elastic\ClientBuilder;
use App\Storage\Elastic\Config;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Transport\Exception\NoNodeAvailableException;

/**
 * Class ElasticEventRepository
 * @package App\Repositories\Elastic
 */
class ElasticEventRepository implements EventRepositoryInterface
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function __construct()
    {
        $this->config = new Config(Application::$app->getConfig());
        $this->client = ClientBuilder::create($this->config);
    }

    /**
     * @inheritdoc
     * @throws MissingParameterException
     * @throws NoNodeAvailableException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function create(Event $event): void
    {
        $this->client->index([
            'index' => $this->config->getStorageName(),
            'id' => $event->getId(),
            'body' => [
                'priority' => $event->getPriority(),
                'conditions' => $event->getPreparedConditions(),
                'event' => $event->getEventInfo()->toArray(),
            ]
        ]);
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(EventSearch $eventSearch): ?Event
    {
        $result = $this->client->search([
            'index' => $this->config->getStorageName(),
            'body' => EventQuery::create($eventSearch),
        ])->asArray();

        $eventBestHit = $result['hits']['hits'][0]['_source'] ?? null;
        if (!$eventBestHit) {
            return null;
        }

        return new Event(
            $eventBestHit['priority'],
            $eventBestHit['event'],
            $eventBestHit['conditions']
        );
    }
}
