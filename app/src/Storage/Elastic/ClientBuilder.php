<?php

declare(strict_types=1);

namespace App\Storage\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder as ElasticClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class ClientBuilder
 * @package App\Storage\Elastic
 */
class ClientBuilder
{
    /**
     * @param Config $config
     * @return Client
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public static function create(Config $config): Client
    {
        $host = "https://{$config->getHost()}:{$config->getPort()}";

        $client = ElasticClientBuilder::create()
            ->setHosts([$host])
            ->setBasicAuthentication($config->getUserName(), $config->getPassword())
            ->setSSLVerification(false)
            ->build();

        if (!self::isIndexExists($client, $config)) {
            self::createIndex($client, $config);
        }

        return $client;
    }

    /**
     * @param Client $client
     * @param Config $config
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    private static function isIndexExists(Client $client, Config $config): bool
    {
        return $client->indices()->exists(['index' => $config->getStorageName()])->asBool();
    }

    /**
     * @param Client $client
     * @param Config $config
     * @return void
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    private static function createIndex(Client $client, Config $config): void
    {
        $client->indices()->create([
            'index' => $config->getStorageName(),
            'body' => $config->getStorageSettings(),
        ]);
    }
}
