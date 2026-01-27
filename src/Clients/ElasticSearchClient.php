<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Clients;

use Dinargab\Homework12\Configuration;
use Elastic\Elasticsearch\Client;

class ElasticSearchClient
{
    private Client $client;

    public function __construct(Configuration $config)
    {
        $this->client = \Elastic\Elasticsearch\ClientBuilder::create()
        ->setHosts([$config->getElasticHost()])
        ->build();
    }


    public function getClient(): Client
    {
        return $this->client;
    }
}