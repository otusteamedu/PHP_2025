<?php

namespace Blarkinov\ElasticApp\Service\ElasticSearch;

class Filter
{
    public function get(array $params): array
    {
        $filter = [];
        $must = [];

        if (!empty($params[ElasticSearch::PARAM_FILTER_CATEGORY]))
            $filter['term']['category'] = $params[ElasticSearch::PARAM_FILTER_CATEGORY];

        if ($params[ElasticSearch::PARAM_FILTER_MIN_PRICE] !== -1)
            $filter['range']['price']['gte'] =  $params[ElasticSearch::PARAM_FILTER_MIN_PRICE];

        if ($params[ElasticSearch::PARAM_FILTER_MAX_PRICE] !== -1)
            $filter['range']['price']['lte'] = $params[ElasticSearch::PARAM_FILTER_MAX_PRICE];

        if ($params[ElasticSearch::PARAM_FILTER_ENABLED])
            $must = [
                "nested" => [
                    "path" => "stock",
                    "query" => [
                        "range" => [
                            "stock.stock" => [
                                "lte" => 0
                            ]
                        ]
                    ]
                ]
            ];

        return ['filter' => $filter, 'must' => $must];
    }
}
