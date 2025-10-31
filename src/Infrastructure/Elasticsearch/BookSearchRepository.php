<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch;

use App\Application\DTO\SearchRequest;
use App\Application\Port\BookSearchRepositoryInterface;
use App\Domain\Book;
use App\Domain\StockItem;
use App\Infrastructure\Config;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

final readonly class BookSearchRepository implements BookSearchRepositoryInterface
{
    public function __construct(
        private Client $client,
        private Config $config
    ) {}

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(SearchRequest $request): array
    {
        $index = (string)$this->config->get('ELASTIC_INDEX', 'otus-shop');

        $must = [];
        $filter = [];

        if ($request->query !== null && $request->query !== '') {
            $must[] = [
                'match' => [
                    'title' => [
                        'query' => $request->query,
                        'fuzziness' => 'AUTO',
                        'operator' => 'and',
                    ],
                ],
            ];
        }

        if ($request->category !== null && $request->category !== '') {
            $filter[] = ['term' => ['category' => $request->category]];
        }

        if ($request->exactPrice !== null) {
            $filter[] = ['term' => ['price' => $request->exactPrice]];
        } else {
            $range = [];
            if ($request->minPrice !== null) {
                $range['gte'] = $request->minPrice;
            }
            if ($request->maxPrice !== null) {
                $range['lte'] = $request->maxPrice;
            }
            if ($range !== []) {
                $filter[] = ['range' => ['price' => $range]];
            }
        }

        if ($request->inStockOnly) {
            $filter[] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => ['gte' => 1],
                        ],
                    ],
                ],
            ];
        }

        $body = [
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter,
                ],
            ],
            'from' => $request->from,
            'size' => $request->size,
        ];

        $response = $this->client->search([
            'index' => $index,
            'body' => $body,
        ]);

        $result = $response->asArray();
        $hits = $result['hits']['hits'] ?? [];
        $items = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'] ?? [];
            $score = isset($hit['_score']) ? (float)$hit['_score'] : null;
            $stockItems = [];
            foreach (($source['stock'] ?? []) as $s) {
                $stockItems[] = new StockItem((string)($s['shop'] ?? ''), (int)($s['stock'] ?? 0));
            }
            $book = new Book(
                (string)($source['title'] ?? ''),
                (string)($source['sku'] ?? ''),
                (string)($source['category'] ?? ''),
                (int)($source['price'] ?? 0),
                $stockItems
            );
            $items[] = ['book' => $book, 'score' => $score];
        }

        $books = array_map(static fn($row) => $row['book'], $items);
        $maxScore = isset($result['hits']['max_score']) ? (float)$result['hits']['max_score'] : null;

        return [
            'items' => $books,
            'total' => (int)($result['hits']['total']['value'] ?? count($books)),
            'max_score' => $maxScore,
        ];
    }
}
