<?php declare(strict_types=1);

namespace App\Elastic\Repositories;

use App\Application;
use App\Elastic\ClientBuilder;
use App\Elastic\Config;
use App\Repositories\ShopStorageRepositoryInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class ShopStorageRepository
 * @package App\Elastic\Repositories
 */
class ShopStorageRepository implements ShopStorageRepositoryInterface
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
    public function create(): array
    {
        return $this->client->indices()->create([
            'index' => $this->config->getStorageName(),
            'body' => $this->config->getStorageSettings(),
        ])->asArray();
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function addItems(string $items): array
    {
        return $this->client->bulk([
            'index' => $this->config->getStorageName(),
            'body' => $items,
        ])->asArray();
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function delete(): array
    {
        return $this->client->indices()->delete([
            'index' => $this->config->getStorageName(),
        ])->asArray();
    }
}
