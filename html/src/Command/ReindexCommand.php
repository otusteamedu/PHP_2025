<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Otus\Elasticsearch\DataReader\JsonReader;

readonly class ReindexCommand
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([
                'https://localhost:9200',
            ])
            ->setSSLVerification(false)
            ->setBasicAuthentication('elastic', 'elastic')
            ->build();
    }

    /**
     * @param string $path
     *
     * @return int
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function __invoke(string $path): int
    {
        $reader = new JsonReader($path);

        while ($data = $reader->getData()) {
            $this->client->bulk($data);
        }

        return 0;
    }
}
