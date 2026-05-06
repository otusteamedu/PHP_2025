<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent;

interface QueryComponentInterface
{
    public function toArray(): array;
}
