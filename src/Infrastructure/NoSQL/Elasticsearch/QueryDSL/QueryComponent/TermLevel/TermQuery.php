<?php

declare(strict_types=1);

namespace App\Infrastructure\NoSQL\Elasticsearch\QueryDSL\QueryComponent\TermLevel;

use App\Infrastructure\NoSQL\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class TermQuery implements QueryComponentInterface
{
    public function __construct(
        private readonly string $field,
        private readonly string $value,
    ) {
    }

    public function toArray(): array
    {
        return [
            'term' => [
                $this->field => $this->value,
            ],
        ];
    }
}
