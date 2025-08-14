<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

use Dotenv\Dotenv;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticClient
{
    private Client $client;

    public function __construct()
    {
        Dotenv::createImmutable(__DIR__ . '/../..')->load();

        $this->client = ClientBuilder::create()->setHosts([$_ENV['HOST'].':'.$_ENV['PORT']])->setBasicAuthentication($_ENV['USERNAME'], $_ENV['PASSWORD'])->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
