<?php

namespace app\Services;

use app\Elasticsearch\ElasticsearchClient;

class BookService
{
    private ElasticsearchClient $client;

    public function __construct(ElasticsearchClient $client) {
        $this->client = $client;
    }

    public function createIndex($indexFile): string
    {
        $response = $this->client->request('PUT', '/otus-shop', file_get_contents($indexFile));
        $responseData = json_decode($response, true);
        if (isset($responseData['acknowledged']) && $responseData['acknowledged'] === true) {
            return "Индекс 'otus-shop' успешно создан.\n";
        } else {
            return "Ошибка при создании индекса: " . $response;
        }
    }

    public function bulkInsert($contentFile): string
    {
        $response = $this->client->request('POST', '/_bulk', file_get_contents($contentFile));
        $responseData = json_decode($response, true);
        if (isset($responseData['errors']) && $responseData['errors'] === false) {
            return "Данные успешно загружены.\n";
        } else {
            return "Ошибки при загрузке данных:\n" . print_r($responseData, true);
        }
    }

    public function searchBooks($title, $shop, $price, $stock) {
        $query = [
            "query" => [
                "bool" => [
                    "must" => [
                        [
                            "match" => [
                                "title" => [
                                    "query" => $title,
                                    "fuzziness" => "2"
                                ]
                            ]
                        ],
                        [
                            "nested" => [
                                "path" => "stock",
                                "query" => [
                                    "bool" => [
                                        "must" => [
                                            [
                                                "match" => [
                                                    "stock.shop" => $shop
                                                ]
                                            ],
                                            [
                                                "range" => [
                                                    "stock.stock" => [
                                                        "gte" => $stock
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "range" => [
                                "price" => [
                                    "lte" => $price
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "sort" => [
                [
                    "price" => [
                        "order" => "asc"
                    ]
                ]
            ]
        ];

        $jsonQuery = json_encode($query, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $response = $this->client->request('GET', '/otus-shop/_search', $jsonQuery);
        return json_decode($response, true);
    }
}