<?php

namespace Elisad5791\Phpapp;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;

class ElasticsearchWrapper implements ElasticSearchClientInterface
{
    private $client;
    private $response;

    public function __construct(Client $client) 
    {
        $this->client = $client;
        $this->response = new Elasticsearch();
    }

    public function get(array $params): array
    {
        return $this->client->get($params)->asArray();
    }

    public function search(array $params): array
    {
        return $this->client->search($params)->asArray();
    }

    public function bulk(array $params): void
    {
        $this->client->bulk($params);
    }

    public function delete(array $params): void
    {
        $this->client->delete($params);
    }
}