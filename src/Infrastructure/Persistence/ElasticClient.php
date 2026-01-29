<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Persistence;

use Dinargab\Homework14\Infrastructure\App\Configuration;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticClient
{
    private Client $client;

    public function __construct(
        private Configuration $configuration,
    ) {
        $this->client = ClientBuilder::create()->setHosts([$this->configuration->getElasticHost()])->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }


}