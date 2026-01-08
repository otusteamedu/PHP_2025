<?php

namespace App\Repository;

use Elastic\Elasticsearch\Client;

class BookRepository {
    private Client $client;
    private string $index = 'otus-shop'; 

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Поиск книг по названию/категории с фильтром по цене
     */
    public function search(string $queryText, ?float $maxPrice = null): array {
        $params = [
            'index' => $this->index,
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query'  => $queryText,
                                    'fields' => ['title^3', 'category'], // Заголовок в 3 раза важнее категории
                                    'fuzziness' => 'AUTO' // Позволяет находить слова с опечатками
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Если указана цена, добавляем фильтр
        if ($maxPrice !== null) {
            $params['body']['query']['bool']['filter'][] = [
                'range' => [
                    'price' => ['lte' => $maxPrice]
                ]
            ];
        }

        return $this->client->search($params)->asArray();
    }

    /**
     * Массовая вставка данных
     */
    public function bulkIndex(array $documents): void {
        $params = ['body' => []];

        foreach ($documents as $doc) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index
                ]
            ];

            $params['body'][] = [
                'title'    => $doc['title'],
                'category' => $doc['category'],
                'price'    => (float)$doc['price'],
                'stock'    => $doc['stock'], // В JSON это может быть массив или число
            ];
        }

        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }
    }
}