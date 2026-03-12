<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent;

interface QueryComponentInterface
{
    public function toArray(): array;
}
