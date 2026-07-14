<?php

declare(strict_types=1);

namespace App\Core\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\Joining;

use App\Core\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class NestedQuery implements QueryComponentInterface
{
    public function __construct(
        private readonly string $path,
        private readonly QueryComponentInterface $query,
    ) {
    }

    public function toArray(): array
    {
        return [
            'nested' => [
                'path' => $this->path,
                'query' => $this->query->toArray(),
            ],
        ];
    }
}
