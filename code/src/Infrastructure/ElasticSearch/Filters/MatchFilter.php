<?php

namespace Otus\Code\Infrastructure\Elasticsearch\Filters;

class MatchFilter implements FilterInterface {
     public static function getFilter(string $filterName, mixed $filterValue = null, string $realFieldName = '') {
        return [
            "match" => [
                $filterName => [
                    "query" => $filterValue,
                    "fuzziness" => "auto"
                ]
            ]
        ];
    }
}