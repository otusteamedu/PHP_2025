<?php

declare(strict_types=1);

namespace App\Infrastructure\NoSQL\Elasticsearch\QueryDSL\QueryComponent\TermLevel;

use App\Infrastructure\NoSQL\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class RangeQuery implements QueryComponentInterface
{
    public const string LT = 'lt';
    public const string GT = 'gt';
    public const string LTE = 'lte';
    public const string GTE = 'gte';

    public function __construct(
        private readonly string $field,
        private readonly array $conditions,
    ) {
    }

    public function toArray(): array
    {
        return  [
            'range' => [
                $this->field => $this->conditions,
            ],
        ];
    }
}
