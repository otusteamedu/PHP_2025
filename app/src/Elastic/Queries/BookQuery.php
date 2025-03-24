<?php declare(strict_types=1);

namespace App\Elastic\Queries;

use App\Elastic\Query;
use App\Forms\Search\BookSearch;

/**
 * Class BookQuery
 * @package App\Elastic\Queries
 */
class BookQuery
{
    /**
     * @param BookSearch $bookSearch
     * @return array
     */
    public static function create(BookSearch $bookSearch): array
    {
        $query = new Query();

        if ($bookSearch->title) {
            $query->addMustQuery(
                $query->prepareMatch('title', $bookSearch->title, [
                    'fuzziness' => 'auto',
                ])
            );
        }

        if ($bookSearch->category) {
            $query->addFilterQuery(
                $query->prepareTerm('category', $bookSearch->category)
            );
        }

        if ($bookSearch->price_min) {
            $query->addFilterQuery(
                $query->prepareRange('price', 'gte', $bookSearch->price_min)
            );
        }

        if ($bookSearch->price_max) {
            $query->addFilterQuery(
                $query->prepareRange('price', 'lte', $bookSearch->price_max)
            );
        }

        if ($bookSearch->stock_shop || $bookSearch->stock_min) {
            $query->addFilterQuery(
                self::prepareStock(
                    $query,
                    $bookSearch->stock_shop,
                    $bookSearch->stock_min
                )
            );
        }

        return $query->getQuery();
    }

    /**
     * @param Query $query
     * @param string $stockShop
     * @param int $stockMin
     * @return array|array[]
     */
    private static function prepareStock(Query $query, string $stockShop, int $stockMin): array
    {
        $result = [
            'nested' => [
                'path' => 'stock',
                'query' => [
                    'bool' => [
                        'filter' => [],
                    ],
                ],
            ],
        ];

        if ($stockShop) {
            $result['nested']['query']['bool']['filter'][] = $query->prepareMatch('stock.shop', $stockShop);
        }

        if ($stockMin) {
            $result['nested']['query']['bool']['filter'][] = $query->prepareRange('stock.stock', 'gte', $stockMin);
        }

        return $result;
    }
}
