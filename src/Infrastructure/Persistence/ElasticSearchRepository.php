<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Persistence;

use Dinargab\Homework14\Domain\Entity\Book;
use Dinargab\Homework14\Domain\Factory\BookFactoryInterface;
use Dinargab\Homework14\Domain\Repository\BookSearchRepositoryInterface;
use Dinargab\Homework14\Infrastructure\App\Configuration;

class ElasticSearchRepository implements BookSearchRepositoryInterface
{

    public function __construct(
        private BookFactoryInterface $bookFactory,
        private ElasticClient $elasticSearchClient,
        private Configuration $config
    ) {
    }

    public function search(
        string $searchQuery,
        ?int $minPrice = null,
        ?int $maxPrice = null,
        ?string $category = null,
        bool $inStock = false
    ): array {
        $params = [
            "index" => $this->config->getIndexName(),
            "body"  => [
                "query" => [
                    "bool" => [
                        "must" => [
                            "match" => [
                                "title" => [
                                    "query"     => $searchQuery,
                                    "fuzziness" => "AUTO"
                                ]
                            ]
                        ],
                    ]

                ]
            ]
        ];
        //Фильтр по ценам
        if ( ! is_null($minPrice)) {
            $params["body"]["query"]["bool"]["filter"][] = [
                "range" => [
                    "price" => [
                        "gte" => $minPrice
                    ]
                ]
            ];
        }
        if ( ! is_null($maxPrice)) {
            $params["body"]["query"]["bool"]["filter"][] = [
                "range" => [
                    "price" => [
                        "lte" => $maxPrice
                    ]
                ]
            ];
        }
        //Отфильтровывает по категории
        if ( ! is_null($category) && ! empty($category)) {
            $params["body"]["query"]["bool"]["filter"][] = [
                "term" => [
                    "category" => $category
                ]
            ];
        }
        //Показать товары только в наличии
        if ($inStock) {
            $params["body"]["query"]["bool"]["filter"][] = [
                "nested" => [
                    "path"  => "stock",
                    "query" => [
                        "range" => [
                            "stock.stock" => [
                                "gt" => 0
                            ]
                        ]
                    ]
                ]
            ];
        }
        $result      = $this->elasticSearchClient->getClient()->search($params)->asArray();
        $resultBooks = [];
        foreach ($result["hits"]["hits"] as $bookDbItem) {
            $bookInfo      = $bookDbItem["_source"];
            $resultBooks[] = $this->bookFactory->create(
                $bookInfo['title'],
                $bookInfo['sku'],
                $bookInfo['category'],
                $bookInfo['price'],
                $bookInfo['stock']
            );
        }

        return $resultBooks;
    }

    public function save(Book $book): void
    {
        // Реализация для отдельных документов
        $this->elasticSearchClient->getClient()->index([
            'index' => $this->config->getIndexName(),
            'id'    => $book->getSku(), // или другой ID
            'body'  => $this->convertToArray($book)
        ]);
    }


    /**
     * Get a book by its SKU
     *
     * @param string $sku Book SKU identifier
     *
     * @return Book Book object
     */
    public function getBySku(string $sku): ?Book
    {
        $params    = [
            'index' => $this->config->getIndexName(),
            'id'    => $sku
        ];
        $result    = $this->elasticSearchClient->getClient()->get($params);
        $bookArray = $result->asArray()["_source"];

        return $this->bookFactory->create(
            $bookArray['title'],
            $bookArray['sku'],
            $bookArray['category'],
            $bookArray['price'],
            $bookArray['stock']
        );
    }

    public function bulkSave(array $books): void
    {
        if (empty($books)) {
            return;
        }

        $params = ['body' => []];

        foreach ($books as $book) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->config->getIndexName(),
                    '_id'    => $book->getSku()->getValue()
                ]
            ];

            $params['body'][] = $this->convertToArray($book);
        }

        $this->elasticSearchClient->getClient()->bulk($params);
    }

    public function clear(): void
    {
        $indexName = $this->config->getIndexName();
        if ($this->elasticSearchClient->getClient()->indices()->exists(['index' => $indexName])->getStatusCode(
            ) === 200) {
            $this->elasticSearchClient->getClient()->indices()->delete(['index' => $indexName]);
        }

        $this->createIndex();
    }


    private function createIndex(): void
    {
        // Создаём с маппингом поэтому подойдет только этот файл с БД
        $this->elasticSearchClient->getClient()->indices()->create([
            "index" => $this->config->getIndexName(),
            "body"  => [
                "mappings" => [
                    "properties" => [
                        "title"    => ["type" => "text"],
                        "sku"      => ["type" => "text"],
                        "category" => ["type" => "keyword"],
                        "price"    => ["type" => "integer"],
                        "stock"    => [
                            "type"       => "nested",
                            "properties" => [
                                "shop"  => ["type" => "keyword"],
                                "stock" => ["type" => "integer"]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function convertToArray(Book $book): array
    {
        return [
            'title'    => $book->getTitle(),
            'sku'      => $book->getSku()->getValue(),
            'category' => $book->getCategory(),
            'price'    => $book->getPrice(),
            'stock'    => $book->getStock()->toArray()
        ];
    }
}