<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Factory;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

final class ESCFactory
{
    /**
     * @return Client
     *
     * @throws AuthenticationException
     */
    public static function factory(): Client
    {
        return ClientBuilder::create()
            ->setHosts([
                env('ES_HOST', 'https://localhost:9200'),
            ])
            ->setSSLVerification(false)
            ->setBasicAuthentication(env('ES_USERNAME', 'elastic'), env('ES_PASSWORD', 'elastic'))
            ->build();
    }
}
