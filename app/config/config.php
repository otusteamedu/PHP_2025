<?php

return [
    'elasticHost' => getenv('ELASTIC_CONTAINER_NAME'),
    'elasticPort' => getenv('ELASTIC_PORT'),
    'elasticUsername' => getenv('ELASTIC_USERNAME'),
    'elasticPassword' => getenv('ELASTIC_PASSWORD'),
    'indexSettings' => [
        'analysis' => [
            'filter' => [
                'ru_stop' => [
                    'type' => 'stop',
                    'stopwords' => '_russian_'
                ],
                'ru_stemmer' => [
                    'type' => 'stemmer',
                    "language" => 'russian'
                ],
            ],
            'analyzer' => [
                'my_russian' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'ru_stop',
                        'ru_stemmer',
                    ],
                ],
            ],
        ],
    ],

    'indexMappings' => [
        'properties' => [
            'sku' => [
                'type' => 'keyword',
            ],
            'category' => [
                'type' => 'keyword',
            ],
            'price' => [
                'type' => 'integer',
            ],
            'title' => [
                'type' => 'text',
                'analyzer' => 'my_russian'
            ],
            'stock' => [
                'type' => 'nested',
                'properties' => [
                    'shop' => [
                        'type' => 'keyword',
                    ],
                    'stock' => [
                        'type' => 'short',
                    ],
                ],
            ],
        ],
    ],
];
