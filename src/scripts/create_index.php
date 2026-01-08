<?php

require '../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();

$params = [
    'index' => 'books',
    'body'  => [
        'settings' => [
            'analysis' => [
                'analyzer' => [
                    'russian_analyzer' => [
                        'type' => 'russian', // Встроенный анализатор для морфологии
                    ]
                ]
            ]
        ],
        'mappings' => [
            'properties' => [
                'title'    => ['type' => 'text', 'analyzer' => 'russian_analyzer'],
                'category' => ['type' => 'keyword'], // Для фильтрации лучше keyword
                'price'    => ['type' => 'float'],
                'stock'    => ['type' => 'integer']
            ]
        ]
    ]
];

// создадим базу с предустановленными настроиками
$client->indices()->create($params);

