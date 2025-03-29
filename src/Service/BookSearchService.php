<?php declare(strict_types=1);

namespace App\Service;

use Elastic\Elasticsearch\Client;

class BookSearchService
{
    private ElasticsearchService $elasticsearchService;
    private Client $client;
    private readonly string $indexName;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->indexName = 'books';
        $this->elasticsearchService = $elasticsearchService;
        $this->client = $this->elasticsearchService->getClient();
    }

    public function checkIfIndexExists(): bool
    {
        return $this->elasticsearchService->indexExists($this->indexName);
    }

    public function fill(): void
    {
        $this->elasticsearchService->createIndex($this->indexName, __DIR__ . '/../../books_mapping.json');
        $this->elasticsearchService->bulkInsertFromJson(__DIR__ . '/../../books.json');
    }

    public function delete(): void
    {
        $this->elasticsearchService->deleteIndex($this->indexName);
    }

    public function search(string $query, ?string $category = null, ?int $priceFrom = null, ?int $priceTo = null, int $size = 10): array
    {
        $must = [
            [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'category'],
                    'fuzziness' => 'auto',
                    'operator' => 'and'
                ]
            ]
        ];

        if ($category) {
            $must[] = [
                'match' => [
                    'category' => [
                        'query' => $category,
                        'fuzziness' => 'auto',
                    ]
                ]
            ];
        }

        if ($priceFrom !== null || $priceTo !== null) {
            $rangeFilter = [];

            if ($priceFrom !== null) {
                $rangeFilter['gte'] = $priceFrom;
            }
            if ($priceTo !== null) {
                $rangeFilter['lte'] = $priceTo;
            }

            $must[] = [
                'range' => [
                    'price' => $rangeFilter
                ]
            ];
        }

        $params = [
            'index' => $this->indexName,
            'body' => [
                'size' => $size,
                'query' => [
                    'bool' => [
                        'must' => $must
                    ]
                ]
            ]
        ];

        return $this->client->search($params)->asArray();
    }
}
