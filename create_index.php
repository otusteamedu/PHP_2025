<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

$params = [
    'index' => 'otus-shop',
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

$response = $client->indices()->create($params);
print_r($response);
