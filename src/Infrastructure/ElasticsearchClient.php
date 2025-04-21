<?php

namespace App\Infrastructure;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticsearchClient
{
    public static function create(): ?Client
    {
        $projectName = \getenv('PROJECT_NAME');
        $host = \getenv('ELASTIC_CONTAINER_NAME');
        $password = \getenv('ELASTIC_PASSWORD');
        $port = \getenv('ELASTIC_PORT');
        $security = \getenv('ELASTIC_SECURITY');
        $hosts = ['http' . ($security === 'true' ? 's' : '') . '://' . $projectName . '_' . $host . ':' . $port];

        try {
            $client = ClientBuilder::create()
                ->setHosts($hosts)
                ->build();
        } catch (AuthenticationException $e) {
            return null;
        }

        return $client;
    }
}
