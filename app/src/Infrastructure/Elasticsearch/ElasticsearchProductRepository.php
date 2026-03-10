<?php
declare(strict_types=1);

namespace Pryaniki\App\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Pryaniki\App\Application\DTO\SearchProductDTO;
use Pryaniki\App\Domain\Repositories\ProductRepositoryInterface;

class ElasticsearchProductRepository implements ProductRepositoryInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search(SearchProductDTO $dto): array
    {
        $must = [];
        $filter = [];
        $should = [];

        if ($dto->query !== null) {
            $should[] = [
                'multi_match' => [
                    'query' => $dto->query,
                    'fields' => ['title'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        if ($dto->category !== null) {
            $filter[] = [
                'term' => [
                    'category' => $dto->category
                ]
            ];
        }

        if ($dto->price !== null) {
            $filter[] = [
                'term' => [
                    'price' => $dto->price
                ]
            ];
        } elseif ($dto->priceFrom !== null || $dto->priceTo !== null) {

            $range = [];

            if ($dto->priceFrom !== null) {
                $range['gte'] = $dto->priceFrom;
            }

            if ($dto->priceTo !== null) {
                $range['lte'] = $dto->priceTo;
            }

            $filter[] = [
                'range' => [
                    'price' => $range
                ]
            ];
        }

        $isNeedAddStockAndShopParameters = $dto->stock !== null
            || $dto->stockFrom !== null
            || $dto->stockTo !== null
            || $dto->shop !== null;

        if ($isNeedAddStockAndShopParameters) {
            $stockMust = [];

            if ($dto->shop !== null) {
                $stockMust[] = [
                    'term' => [
                        'stock.shop' => $dto->shop
                    ]
                ];
            }

            if ($dto->stock !== null) {
                $stockMust[] = [
                    'term' => [
                        'stock.stock' => $dto->stock
                    ]
                ];
            } else {
                $range = [];

                if ($dto->stockFrom !== null) {
                    $range['gte'] = $dto->stockFrom;
                }

                if ($dto->stockTo !== null) {
                    $range['lte'] = $dto->stockTo;
                }

                if ($range) {
                    $stockMust[] = [
                        'range' => [
                            'stock.stock' => $range
                        ]
                    ];
                }
            }

            $filter[] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'bool' => [
                            'must' => $stockMust
                        ]
                    ]
                ]
            ];
        }

        $query = [
            'bool' => array_filter([
                'must' => $must,
                'filter' => $filter,
                'should' => $should,
                'minimum_should_match' => $should ? 1 : null
            ])
        ];

        $response = $this->client->search([
            'index' => ElasticsearchIndexManager::INDEX_NAME,
            'body' => ['query' => $query]
        ]);

        return $response['hits']['hits'];
    }
}