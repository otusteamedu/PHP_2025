<?php

namespace Otus\Code\Infrastructure\Elasticsearch\Filters;

interface FilterInterface {
    public static function getFilter(string $filterName, mixed $filterValue = null, string $realFieldName = '');
}