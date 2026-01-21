<?php

namespace App\ES\Repository;

use App\ES\ElasticsearchClientProvider;
use App\ES\Enum\ComparisonSign;
use App\ES\Query\Constructor\BoolConstructor;
use App\ES\Query\Constructor\NestedConstructor;
use App\ES\Query\Filter\MatchFilter;
use App\ES\Query\Filter\RangeFilter;
use App\ES\Query\Filter\TermFilter;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class BookRepository
{
    /**
     * Возвращает книги в наличии
     *
     * @param string $title
     * @param string $shop
     * @param int|float $minPrice
     * @param int|float|null $maxPrice
     * @param string|null $category
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getBooksInStock(
        string $title,
        string $shop,
        int|float $minPrice = 0,
        int|float|null $maxPrice = null,
        string|null $category = null,
    ): array
    {
        $client = ElasticsearchClientProvider::getInstance()->getClient();

        $rootQuery = (new BoolConstructor())
            ->must(new MatchFilter('title', $title, ['fuzziness' => 'auto',]))
            ->must(
                (new NestedConstructor('stock'))->add(
                    (new BoolConstructor())
                        ->must(new MatchFilter('stock.shop', $shop))
                        ->must(new RangeFilter('stock.stock', 0, ComparisonSign::GreaterThan))
                )
            );

        if ($minPrice > 0) {
            $rootQuery->filter(new RangeFilter('price', $minPrice, ComparisonSign::GreaterThanOrEqual));
        }

        if ($maxPrice !== null) {
            $rootQuery->filter(new RangeFilter('price', $maxPrice, ComparisonSign::LessThanOrEqual));
        }

        if ($category !== null && $category !== '') {
            $rootQuery->filter(new TermFilter('category', $category));
        }

        $params = [
            'index' => $_ENV['PRODUCT_INDEX'],
            'body'  => [
                'query' => $rootQuery->build(),
            ],
        ];

        $response = $client->search($params)->asArray();
        return $response['hits']['hits'] ?? [];
    }
}
