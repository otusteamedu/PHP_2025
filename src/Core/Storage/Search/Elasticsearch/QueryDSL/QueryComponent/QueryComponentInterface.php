<?php

declare(strict_types=1);

namespace App\Core\Storage\Search\Elasticsearch\QueryDSL\QueryComponent;

interface QueryComponentInterface
{
    public function toArray(): array;
}
