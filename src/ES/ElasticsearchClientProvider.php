<?php

namespace App\ES;

use App\Base\Singleton;
use App\ES\Config\BaseConfiguration;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchClientProvider extends Singleton
{
    protected Client $oInstanceES;
    protected BaseConfiguration $configuration;

    protected function __construct()
    {
        $this->configuration = new BaseConfiguration();

        $this->oInstanceES = ClientBuilder::create()
            ->setHosts(explode(',', (string) $_ENV['ELASTIC_DOMAIN']))
            ->setBasicAuthentication($_ENV['ELASTIC_USERNAME'], $_ENV['ELASTIC_PASSWORD'])
            ->build();
    }


    public function getClient(): Client
    {
        return $this->oInstanceES;
    }
}