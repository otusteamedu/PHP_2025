<?php

namespace Otus\Code\Infrastructure\Elasticsearch\Filters;

class NestedFilter implements FilterInterface {
    public static function getFilter(string $filterName, mixed $filterValue = null, string $realFieldName = '') {
        return 
            [
                "nested" => [
                    "path" => $realFieldName,
                    "query" => [
                        "bool" => [
                            "filter" => [
                                [
                                    "range" => [
                                        "$realFieldName.stock" => [
                                            "gt" => 0
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
    }
}