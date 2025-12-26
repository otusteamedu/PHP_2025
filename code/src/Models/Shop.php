<?php

namespace Ak\Hw\Models;

use Ak\Hw\Models\Elastic;
use JsonException;

class Shop
{
    protected static ?Elastic $elastic = null;

    private static function initElastic(): void
    {
        if (self::$elastic === null) {
            self::$elastic = new Elastic('otus-shop');
        }
    }

    /**
     * @throws JsonException
     */
    public static function getCategories(): array
    {
        self::initElastic();
        $result = array('Без категории');

        $query = [
            "_source" => ["category"],
            'aggs' => [
                'categories' => [
                    'terms' => [
                        'field' => 'category.keyword',
                        'size' => 10000
                    ]
                ]
            ]
        ];

        $response = self::$elastic->search($query);
        $hits = $response['hits']['hits'];

        foreach ($hits as $hit) {
            if(!in_array($hit['_source']['category'], $result, true)){
                $result[] = $hit['_source']['category'];
            }
        }

        return $result;
    }


    /**
     * @throws JsonException
     */
    public static function find($category, $price, $title)
    {
        self::initElastic();

        $query = [
            'query' => [
                'bool' => [],
            ],
        ];

        if ($category !== 'Без категории') {
            $query['query']['bool']['must'][] = [
                'match' => [
                    'category' => [
                        'query' => $category,
                    ],
                ],
            ];
        }

        if (!empty($title)) {
            $query['query']['bool']['must'][] = [
                'match' => [
                    'title' => [
                        'query' => $title,
                        'fuzziness' => 'auto',
                    ],
                ],
            ];
        }

        if (!empty($price)) {
            $query['query']['bool']['filter'][] = [
                'range' => [
                    'price' => ['lte' => $price]
                ]
            ];
        }

        $response = self::$elastic->search($query);
        if((int)$response['hits']['total'] === 0){
            return false;
        }

        return $response['hits']['hits'];
    }
}
