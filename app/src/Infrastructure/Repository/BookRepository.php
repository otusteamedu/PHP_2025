<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\App;
use App\Domain\Repository\BookRepositoryInterface;
use App\Infrastructure\Service\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class BookRepository implements BookRepositoryInterface
{
    private Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::build(
            App::$app->getConfigValue('elasticHost'),
            App::$app->getConfigValue('elasticPort'),
            App::$app->getConfigValue('elasticUsername'),
            App::$app->getConfigValue('elasticPassword'));
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function dbCreate(string $dbTitle, ?array $mappings = null, ?array $settings = null): bool
    {
        $data = [
            'index' => $dbTitle,
            'body' => [
                'settings' => App::$app->getConfigValue('indexSettings'),
                'mappings' => App::$app->getConfigValue('indexMappings')
            ]
        ];
        if ($mappings) {
            $data['body']['mappings'] = $mappings;
        }
        if ($settings) {
            $data['body']['settings'] = $settings;
        }

        return $this->client->index($data)->asBool();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function dbDelete(string $dbTitle): bool
    {
        $data = [
            'index' => $dbTitle,
        ];

        return $this->client->indices()->delete($data)->asBool();
    }

    public function bulkInsert(string $itemsData, string $dbTitle): array
    {
        return $this->client->bulk([
            'index' => $dbTitle,
            'body' => $itemsData,
        ])->asArray();
    }
}