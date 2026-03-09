<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\Client;

use App\Application\DotEnvLoader;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchClientProvider
{
    public static function get(): Client
    {
        $dotEnvLoader = new DotEnvLoader();
        $esUser = $dotEnvLoader->getEnv('ELASTIC_USER');
        $esPassword = $dotEnvLoader->getEnv('ELASTIC_PASSWORD');
        $esHost = $dotEnvLoader->getEnv('ELASTIC_HOST');
        $certsDir = $dotEnvLoader->getEnv('ELASTIC_CERTS_DIR');

        return ClientBuilder::create()
            ->setHosts(["https://$esHost:9200"])
            ->setBasicAuthentication($esUser, $esPassword)
            ->setCABundle("$certsDir/http_ca.crt")
            ->build();
    }
}
