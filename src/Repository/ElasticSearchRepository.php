<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Repository;

use Dinargab\Homework12\Configuration;
use Dinargab\Homework12\Data\Mapper\BookMapper;
use Dinargab\Homework12\Data\Model\Book;
use Elastic\Elasticsearch\Client;

class ElasticSearchRepository implements RepositoryInterface
{

    private Client $client;
    private Configuration $config;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Search for books with various filters
     * 
     * @param string $searchQuery Search query for book titles
     * @param array $price Price range filter
     * @param string|null $category Category filter
     * @param bool $inStock Whether to filter only books in stock
     * @return Book[] Array of Book objects matching the search criteria
     */
    public function search(string $searchQuery, array $price, ?string $category = "", bool $inStock = false): array
    {

        $params = [
            "index" => $this->config->getIndexName(),
            "body" => [
                "query" => [
                    "bool" => [
                        "must" => [
                            "match" => [
                                "title" => [
                                    "query" => $searchQuery,
                                    "fuzziness" => "AUTO"
                                ]
                            ]
                        ],
                    ]

                ]
            ]
        ];
        //Фильтр по ценам
        if (isset($price) && !empty($price)) {
            if (isset($price["min_price"]) && !empty($price["min_price"])) {
                $params["body"]["query"]["bool"]["filter"][] = [
                    "range" => [
                        "price" => [
                            "gte" => $price["min_price"]
                        ]
                    ]
                ];
            }
            if (isset($price["max_price"]) && !empty($price["max_price"])) {
                $params["body"]["query"]["bool"]["filter"][] = [
                    "range" => [
                        "price" => [
                            "lte" => $price["max_price"]
                        ]
                    ]
                ];
            }
        }
        //Отфильтровывает по категории
        if (!empty($category)) {
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
                    "path" => "stock",
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
        $result = $this->client->search($params)->asArray();
        $resultBooks = [];
        foreach ($result["hits"]["hits"] as $bookDbItem) {
            $resultBooks[] = BookMapper::dbToBook($bookDbItem["_source"]);
        }
        return $resultBooks;
    }

    /**
     * Get a book by its SKU
     * 
     * @param string $sku Book SKU identifier
     * @return Book Book object
     */
    public function getBySku(string $sku): Book
    {
        $params = [
            'index' => $this->config->getIndexName(),
            'id'    => $sku
        ];
        $result = $this->client->get($params);
        return BookMapper::dbToBook($result->asArray()["_source"]);
    }
}
