<?php

namespace App\Repository;

use Elastic\Elasticsearch\Client;

class BookRepository {
    private Client $client;
    private string $index = 'books';

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Массовая вставка данных
     */
    public function bulkIndex(array $documents): void {
        $params = ['body' => []];

        foreach ($documents as $doc) {
            // Первая строка для Bulk API — метаданные (индекс и действие)
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index
                ]
            ];

            // Вторая строка — сами данные
            $params['body'][] = [
                'title'    => $doc['title'],
                'category' => $doc['category'],
                'price'    => (float)$doc['price'],
                'stock'    => (int)$doc['stock'],
            ];
        }

        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }
    }
}