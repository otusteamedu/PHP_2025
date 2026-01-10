<?php

namespace App;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticClient
{
    private Client $client;

    public function __construct(array $hosts = ['localhost:9200'])
    {
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
