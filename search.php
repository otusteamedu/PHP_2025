<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

$query = $argv[1] ?? '';
$maxPrice = $argv[2] ?? null;

if (empty($query)) {
    echo "Не указано слово для поиска.\n";
    exit;
}

$params = [
    'index' => 'otus-shop',
    'body' => [
        'query' => [
            'bool' => [
                'must' => [
                    [
                        'match' => [
                            'title' => [
                                'query' => $query,
                                'fuzziness' => 'AUTO'
                            ]
                        ]
                    ]
                ],
                'filter' => [
                    [
                        'nested' => [
                            'path' => 'stock',
                            'query' => [
                                'range' => [
                                    'stock.stock' => [
                                        'gt' => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

if ($maxPrice !== null) {
    $params['body']['query']['bool']['filter'][] = [
        'range' => [
            'price' => [
                'lte' => $maxPrice
            ]
        ]
    ];
}

$response = $client->search($params);

if (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
    echo "Результаты поиска:\n";
    foreach ($response['hits']['hits'] as $hit) {
        echo "{$hit['_source']['title']} - {$hit['_source']['price']} руб. (Остаток: " . count($hit['_source']['stock']) . " магазинов)\n";
    }
} else {
    echo "Ничего не найдено.\n";
}
