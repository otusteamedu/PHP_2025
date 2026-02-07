<?php

namespace Otus\Code\Infrastructure\Elasticsearch;

class ElasticSearchQuery {

    public static function getQuery(array $search_options, array $map) {
        $queryData = [];
        foreach($search_options as $field => $value) {
            $queryData[$map[$field]['operator']][] = $map[$field]['class']::getFilter($field, $value, $map[$field]['field'] ?? '');
        }

        return ['bool' => $queryData];
    }
}