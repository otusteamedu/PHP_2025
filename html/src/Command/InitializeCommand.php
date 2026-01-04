<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

readonly class InitializeCommand
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
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function __invoke(): int
    {
        $this
            ->client
            ->indices()
            ->create([
                'index' => 'otus-shop',
                'body' => [
                    'settings' => [
                        'analysis' => [
                            'analyzer' => [
                                'ru' => [
                                    'type' => 'russian',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'ru',
                            ],
                            'category' => [
                                'type' => 'text',
                                'analyzer' => 'ru',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword',
                                    ],
                                ],
                            ],
                            'sku' => [
                                'type' => 'keyword',
                            ],
                            'price' => [
                                'type' => 'integer',
                            ],
                            'stock' => [
                                'type' => 'nested',
                                'properties' => [
                                    'shop' => [
                                        'type' => 'keyword',
                                    ],
                                    'stock' => [
                                        'type' => 'integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        return 0;
    }
}
