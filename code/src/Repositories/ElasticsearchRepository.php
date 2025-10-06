<?php

namespace App\Repositories;

use Elastic\Elasticsearch\Client; 
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class ElasticsearchRepository
{
    private Client $client;
    private string $indexName = 'books';

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['http://elasticsearch:9200'])
            ->build();
    }

    /**
     * Создание индекса с правильными настройками для русского языка
     */
    public function createIndex(): void
    {
        $params = [
            'index' => $this->indexName,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'russian' => [
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_stop',
                                    'russian_stemmer'
                                ]
                            ]
                        ],
                        'filter' => [
                            'russian_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_'
                            ],
                            'russian_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian'
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'russian',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'sku' => [
                            'type' => 'keyword'
                        ],
                        'category' => [
                            'type' => 'keyword'
                        ],
                        'price' => [
                            'type' => 'float'
                        ],
                        'stock' => [
                            'type' => 'integer'
                        ],
                        'stock_details' => [
                            'type' => 'nested',
                            'properties' => [
                                'shop' => ['type' => 'keyword'],
                                'stock' => ['type' => 'integer']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            if ($this->client->indices()->exists(['index' => $this->indexName])->asBool()) {
                $this->client->indices()->delete(['index' => $this->indexName]);
            }
            
            $this->client->indices()->create($params);
            echo "Индекс создан успешно\n";
        } catch (\Exception $e) {
            echo "Ошибка при создании индекса: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Заполнение индекса данными
     */
    public function indexBooks(array $books): void
    {
        foreach ($books as $book) {
            $params = [
                'index' => $this->indexName,
                'id' => $book['sku'],
                'body' => $book
            ];
    
            try {
                $this->client->index($params);
            } catch (\Exception $e) {
                echo "Ошибка при индексации книги {$book['sku']}: " . $e->getMessage() . "\n";
            }
        }
    
        $this->client->indices()->refresh(['index' => $this->indexName]);
        echo "Данные проиндексированы\n";
    }

    /**
     * Поиск книг
     */
    public function searchBooks(string $query, ?string $category = null, ?float $maxPrice = null, bool $inStock = false): array
    {
        $searchParams = [
            'index' => $this->indexName,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => []
                    ]
                ]
            ]
        ];

        // Текстовый поиск
        if (!empty($query)) {
            $searchParams['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title', 'category'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        // Фильтр по категории
        if ($category) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['category' => $category]
            ];
        }

        // Фильтр по цене
        if ($maxPrice) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['lte' => $maxPrice]]
            ];
        }

        // Фильтр по наличию
        if ($inStock) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['stock' => ['gt' => 0]]
            ];
        }

        try {
            $response = $this->client->search($searchParams);
            return $response->asArray();
        } catch (ClientResponseException $e) {
            echo "Ошибка клиента: " . $e->getMessage() . "\n";
        } catch (ServerResponseException $e) {
            echo "Ошибка сервера: " . $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
        }

        return [];
    }
}