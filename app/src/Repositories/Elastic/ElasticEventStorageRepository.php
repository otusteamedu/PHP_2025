<?php

declare(strict_types=1);

namespace App\Repositories\Elastic;

use App\Application;
use App\Repositories\EventStorageRepositoryInterface;
use App\Storage\Elastic\ClientBuilder;
use App\Storage\Elastic\Config;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use stdClass;

/**
 * Class ElasticEventStorageRepository
 * @package App\Repositories\Elastic
 */
class ElasticEventStorageRepository implements EventStorageRepositoryInterface
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
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function clear(): void
    {
        $this->client->deleteByQuery([
            'index' => $this->config->getStorageName(),
            'body' => [
                'query' => [
                    'match_all' => new stdClass()
                ],
            ],
        ]);
    }
}
