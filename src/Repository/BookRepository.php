<?php

namespace App\Repository;

use App\Elasticsearch\ElasticsearchClient;
use Elastic\Elasticsearch\Client;

class BookRepository
{
    private Client $client;
    private string $indexName;

    public function __construct(ElasticsearchClient $elasticsearchClient, string $indexName = 'otus-shop')
    {
        $this->client = $elasticsearchClient->getClient();
        $this->indexName = $indexName;
    }

    /**
     * индексирует bulk-запросом книги
     */
    public function indexBooksBulk(array $bulkBody): void
    {
        $response = $this->client->bulk(['body' => $bulkBody]);
        $result = $response->asArray();


        //проверяем результаты импорта на ошибки
        foreach ($result['items'] as $item) {
            $action = key($item);
            $status = $item[$action]['status'];

            if ($status >= 400) {
                echo "Ошибка $action: " . json_encode($item) . "\n";
            }
        }

        if (!empty($result['errors'])) {
            throw new \RuntimeException("Ошибки при bulk-индексации: " . json_encode($result));
        }
    }


    /**
     * поиск в Elastic
     */
    public function searchBooks(string $query, ?int $maxPrice = null, ?string $category = null): array
    {
        $params = [
            'index' => $this->indexName,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => array_filter([
                            [
                                'multi_match' => [
                                    'query' => $query,
                                    'fields' => ['title'],
                                    'fuzziness' => 'AUTO'
                                ]
                            ],
                            $maxPrice !== null ? [
                                'range' => [
                                    'price' => ['lte' => $maxPrice]
                                ]
                            ] : null,
                            $category !== null ? [
                                'term' => [
                                    'category.keyword' => $category
                                ]
                            ] : null
                        ]),
                        'filter' => [
                            [
                                'bool' => [
                                    'should' => [
                                        ['range' => ['stock.stock' => ['gt' => 1]]]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        file_put_contents('filename.json', json_encode($params, JSON_PRETTY_PRINT));

        return $this->client->search($params)->asArray();
    }
}
