<?php

namespace App;

class Indexer {
    private ElasticClient $elastic;

    public function __construct(ElasticClient $elastic) {
        $this->elastic = $elastic;
    }

    public function createIndex(string $indexName): void {
        $params = [
            'index' => $indexName,
            'body'  => [
                'settings' => [
                    'analysis' => [
                        'tokenizer' => [
                            'standard' => ['type' => 'standard']
                        ],
                        'filter' => [
                            'russian_stop' => ['type' => 'stop', 'stopwords' => '_russian_'],
                            'russian_stemmer' => ['type' => 'stemmer', 'language' => 'russian']
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'russian_stop', 'russian_stemmer']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => ['type' => 'text'],
                        'category' => ['type' => 'text'],
                        'price' => ['type' => 'float'],
                        'stock' => [
                            'type' => 'nested',
                            'properties' => [
                                'shop' => ['type' => 'text'],
                                'stock' => ['type' => 'integer']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client = $this->elastic->getClient();
        $response = $client->indices()->create($params);
        print_r($response);
    }
}
