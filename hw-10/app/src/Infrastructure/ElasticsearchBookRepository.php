<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Book;
use App\Domain\BookRepositoryInterface;
use App\Domain\Shop;
use Elastic\Elasticsearch\ClientBuilder;

final readonly class ElasticsearchBookRepository implements BookRepositoryInterface
{
    public function searchBooks(?string $name, ?string $category, ?int $maxPrice, int $minPrice, bool $inStock): array
    {
        $client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->build();

        $params = [
            'index' => 'otus-shop',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => [],
                    ],
                ],
            ],
        ];

        $priceRange = ['gte' => $minPrice];

        if ($maxPrice !== null) {
            $priceRange['lt'] = $maxPrice;
        }

        $params['body']['query']['bool']['filter'][] = [
            'range' => [
                'price' => $priceRange,
            ],
        ];

        if ($name !== null && $name !== '') {
            $params['body']['query']['bool']['must'][] = [
                'match' => [
                    'title' => [
                        'query' => $name,
                        'operator' => 'and',
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ];
        }

        if ($category !== null && $category !== '') {
            $params['body']['query']['bool']['filter'][] = [
                'term' => [
                    'category' => $category,
                ],
            ];
        }

        if ($inStock) {
            $params['body']['query']['bool']['filter'][] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => [
                                'gt' => 0,
                            ],
                        ],
                    ],
                ],
            ];
        }

        $response = $client->search($params);

        return array_map(
            static fn(array $hit) => new Book(
                title: $hit['_source']['title'],
                sku: $hit['_source']['sku'],
                category: $hit['_source']['category'],
                price: $hit['_source']['price'],
                stock: array_map(
                    fn(array $stock) => new Shop(
                        name: $stock['shop'],
                        countBooks: $stock['stock']
                    ),
                    $hit['_source']['stock']
                )
            ),
            $response['hits']['hits']
        );
    }
}
