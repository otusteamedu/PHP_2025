<?php
declare(strict_types=1);

namespace App\ElasticSearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ClientFactory
{
    private const string ES_HOST = 'elasticsearch:9200';

    /**
     * @return Client
     * @throws AuthenticationException
     */
    public static function create(): Client
    {
        return ClientBuilder::create()
            ->setHosts([self::ES_HOST])
            ->build();
    }
}
