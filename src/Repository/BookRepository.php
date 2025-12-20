<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\Book;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

readonly class BookRepository
{
    private const string ES_INDEX = 'otus-shop';

    private const int ES_SIZE_LIMIT = 100;

    private Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string|null $title
     * @param string|null $category
     * @param int|null $maxPrice
     * @param bool $inStock
     * @return Book[]
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(?string $title, ?string $category, ?int $maxPrice, bool $inStock): array
    {
        $must = [];
        $filter = [];

        if ($title !== null && trim($title) !== '') {
            $must[] =
            [
                'match' => [
                    'title' => [
                        'query' => $title,
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ];
        }

        if ($category !== null && trim($category) !== '') {
            $must[] =
            [
                'term' => [
                    'category' => $category
                ],
            ];
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $filter[] =
            [
                'range' => [
                    'price' => [
                        ['lte' => $maxPrice]
                    ],
                ],
            ];
        }

        if ($inStock) {
            $filter[] =
            [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => ['gt' => 0]
                        ],
                    ],
                ],
            ];
        }

        if (empty($must) && empty($filter)) {
            $query = [
                'match_all' => new \stdClass()
            ];
        } else {
            $query = [
                'bool' => array_filter([
                    'must'   => $must ?: null,
                    'filter' => $filter ?: null,
                ])
            ];
        }

        $response = $this->client->search([
            'index' => self::ES_INDEX,
            'body' => [
                'query' => $query,
                'size' => self::ES_SIZE_LIMIT
            ]
        ]);

        return array_map(
            static fn(array $hit): Book => Book::fromElastic($hit),
            $response['hits']['hits']
        );
    }
}
