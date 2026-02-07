<?php

namespace Otus\Code\Infrastructure\Elasticsearch\Filters;

class RangeFilter implements FilterInterface {
    public static function getFilter(string $filterName, mixed $filterValue = null, string $realFieldName = '') {
        return 
            [
                "range" => [
                    $filterName => [
                        "lte" => $filterValue
                    ]
                ]
            ];
    }
}