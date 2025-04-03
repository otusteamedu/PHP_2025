<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use stdClass;

class ElasticService
{
    /** @var Client */
    protected Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct() {
        $host = getenv('ELASTIC_HOST');
        $user = getenv('ELASTIC_USER');
        $pass = getenv('ELASTIC_PASS');

        $this->client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(["$host:9200"])
            ->setBasicAuthentication($user, $pass)
            ->build();
    }

    /**
     * @return Client
     */
    public function getClient(): Client {
        return $this->client;
    }

    /**
     * @param string $indexName
     * @param array|null $body
     * @return array
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function create(string $indexName, ?array $body): array {
        return $this->getClient()->indices()
            ->create([
                'index' => $indexName,
                'body' => $body,
            ])->asArray();
    }

    /**
     * @param string $indexName
     * @param $id
     * @return array
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function get(string $indexName, $id): array {
        $params = [
            'index' => $indexName,
        ];

        if (is_numeric($id)) {
            $params['id'] = $id;
        }

        return $this->getClient()->indices()->get($params)->asArray();
    }

    /**
     * @param string $json
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulk(string $json): array {
        return $this->getClient()->bulk(['body' => $json])->asArray();
    }

    /**
     * @param string $indexName
     * @param $query
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(string $indexName, $query): array {
        $params = [
            'index' => $indexName,
            'body' => [
                'size' => 1000,
            ],
            'scroll' => '1m'
        ];

        if (empty($query)) {
            $params['body']['query'] = ['match_all' => new stdClass()];
        } else {
            $params['body']['query'] = $query;
        }

        return $this->getClient()->search($params)->asArray();
    }

    /**
     * @param string $indexName
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function exists(string $indexName): bool {
        return $this->getClient()->indices()->exists(['index' => $indexName])->asBool();
    }

    /**
     * @param string $indexName
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function drop(string $indexName): bool {
        return $this->getClient()->indices()->delete(['index' => $indexName])->asBool();
    }
}