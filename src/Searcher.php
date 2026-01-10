<?php

namespace App;

class Searcher {
    private ElasticClient $elastic;

    public function __construct(ElasticClient $elastic) {
        $this->elastic = $elastic;
    }

    public function search(string $query, ?float $maxPrice = null, string $indexName = 'otus-shop'): void {
        if (empty($query)) {
            echo "Не указано слово для поиска.\n";
            return;
        }

        $params = [
            'index' => $indexName,
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
                                            'stock.stock' => ['gt' => 0]
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
                    'price' => ['lte' => $maxPrice]
                ]
            ];
        }

        $client = $this->elastic->getClient();
        $response = $client->search($params);

        if (!empty($response['hits']['hits'])) {
            echo "Результаты поиска:\n";
            foreach ($response['hits']['hits'] as $hit) {
                echo "{$hit['_source']['title']} - {$hit['_source']['price']} руб.\n";
            }
        } else {
            echo "Ничего не найдено.\n";
        }
    }
}
