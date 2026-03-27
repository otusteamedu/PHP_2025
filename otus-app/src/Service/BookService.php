<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\SearchBookParamListDto;
use App\Interface\RepositoryInterface;
use JsonException;
use RuntimeException;
use stdClass;

class BookService
{
    public function __construct(
        private RepositoryInterface $repository,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function addBulk(array $bookList): void
    {
        $response = $this->repository->addBulk($bookList);

        if (!empty($response['errors'])) {
            throw new RuntimeException("An error occurred: " . json_encode($response, JSON_THROW_ON_ERROR));
        }
    }

    public function getBookList(string $action, SearchBookParamListDto $params): array
    {
        $searchQuery = $this->getSearchQuery($action, $params);
        $result = $this->repository->searchByQuery($searchQuery);

        $bookHitList = $result['hits']['hits'] ?? [];
        $bookList = [];

        foreach ($bookHitList as $bookHit) {
            $bookList[] = $bookHit['_source'];
        }

        return $bookList;
    }

    private function getSearchQuery(string $action, SearchBookParamListDto $paramsDto): array
    {
        $preparedFilterList = [];

        if ($paramsDto->query) {
            $preparedFilterList[] = [
                'multi_match' => [
                    'query' => $paramsDto->query,
                    'fields' => ['title'],
                    'fuzziness' => 'auto'
                ],
            ];
        }

        if ($paramsDto->minPrice) {
            $preparedFilterList[] = [
                'range' => [
                    'price' => ['gte' => $paramsDto->minPrice],
                ],
            ];
        }

        if ($paramsDto->maxPrice) {
            $preparedFilterList[] = [
                'range' => [
                    'price' => ['lte' => $paramsDto->maxPrice],
                ],
            ];
        }

        if ($paramsDto->category) {
            $preparedFilterList[] = [
                'term' => [
                    'category' => $paramsDto->category,
                ]
            ];
        }

        return match ($action) {
            'defaultSearch' => [
                'query' => [
                    'bool' => [
                        'must' => $preparedFilterList,
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
            ],
            'getAll' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
            ],
        };
    }

    public function deleteStorage(): void
    {
        $this->repository->delete();
    }

    public function createStorage(): void
    {
        $baseConfig = [
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                    ],
                    'sku' => [
                        'type' => 'keyword',
                    ],
                    'category' => [
                        'type' => 'keyword',
                    ],
                    'price' => [
                        'type' => 'integer',
                    ],
                    'stock' => [
                        'properties' => [
                            'shop' => [
                                'type' => 'keyword',
                            ],
                            'stock' => [
                                'type' => 'integer',
                            ],
                        ],
                    ],
                ]
            ]
        ];

        $this->repository->create($baseConfig);
    }
}
