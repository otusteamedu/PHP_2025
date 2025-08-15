<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticInfo
{
    private const string INDEX_NAME = 'otus-shop';

    public function getIndexName(): string
    {
        return self::INDEX_NAME;
    }

    public function getMappingBody(): array
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
}
