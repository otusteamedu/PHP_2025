<?php

declare(strict_types=1);

namespace App\Repository;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class BaseRepository
{
    protected Client $client;
    protected string $defaultIndex;

    /**
     * @throws AuthenticationException
     */
    public function __construct(
    ) {
        $host = sprintf(
            'https://%s:%s',
            getenv('ELASTIC_HOST'),
            getenv('ELASTIC_PORT'),
        );

        $this->client = ClientBuilder::create()
            ->setHosts([$host])
            ->setBasicAuthentication(getenv('ELASTIC_USERNAME'), getenv('ELASTIC_PASSWORD'))
            ->setSSLVerification(false)
            ->build();
        $this->defaultIndex = getenv('ELASTIC_DEFAULT_INDEX');
    }
}
