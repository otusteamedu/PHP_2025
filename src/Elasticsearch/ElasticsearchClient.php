<?php

namespace App\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use App\Config;

class ElasticsearchClient
{
    private Client $client;

    public function __construct(Config $config)
    {
        $this->client = ClientBuilder::create()
            ->setHosts([$config->getElasticsearchHost()])
            ->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}

