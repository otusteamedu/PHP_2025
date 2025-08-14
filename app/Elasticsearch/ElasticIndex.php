<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticIndex
{
    private ElasticClient $client;

    public function __construct(ElasticClient $client)
    {
        $this->client = $client;
    }

    private function createBody(): array
    {
        return [
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'my_russian'
                    ],
                    'sku' => [
                        'type' => 'keyword',
                    ],
                    'category' => [
                        'type' => 'keyword',
                        'analyzer' => 'my_russian'
                    ],
                    'price' => [
                        'type' => 'integer',
                    ],
                    'stock' => [
                        'type' => 'nested',
                        'properties' => [
                            'shop' => [
                                'type' => 'keyword',
                                'analyzer' => 'my_russian'
                            ],
                            'stock' => [
                                'type' => 'integer'
                            ],
                        ]
                    ]
                ]
            ],
            'settings' => [
                'analysis' => [
                    'filter' => [
                        'ru_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_russian_'
                        ],
                        'ru_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian'
                        ]
                    ],
                    'analyzer' => [
                        'my_russian' => [
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'ru_stop',
                                'ru_stemmer'
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    public function createIndex(): void
    {
        $indexName = new ElasticInfo();

        try {
            $client = $this->client->getClient();

            $params = [
                'index' => $indexName->getIndexName(),
                'body' => $this->createBody(),
            ];

            $response = $client->indices()->create($params);

            echo 'Индекс ' . $response['index'] . ' добавлен.';
        } catch (\Exception) {

            echo 'Индекс ' . $indexName->getIndexName() . ' уже существует.';
        }
    }

    public function deleteIndex(): void
    {
        $indexName = new ElasticInfo();

        try {
            $client = $this->client->getClient();

            $params = [
                'index' => $indexName->getIndexName(),
            ];

            $client->indices()->delete($params);

            echo 'Индекс ' . $indexName->getIndexName() . ' удален.';
        } catch (\Exception) {

            echo 'Индекса ' . $indexName->getIndexName() . ' не существует.';
        }
    }

    public function indexExists(): bool
    {
        $indexName = new ElasticInfo();
        $client = $this->client->getClient();

        return $client->indices()->exists(['index' => $indexName->getIndexName()])->asBool();
    }
}
