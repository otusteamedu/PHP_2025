<?php

namespace Otus\Code\Infrastructure\Elasticsearch\Filters;

class TermFilter implements FilterInterface {
     public static function getFilter(string $filterName, mixed $filterValue = null, string $realFieldName = '') {
        return 
            [
                "term" => [
                    $filterName => $filterValue
                ]
            ];
     }
}