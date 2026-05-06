<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\FullText;

use App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class MatchQuery implements QueryComponentInterface
{
    public function __construct(
        private readonly string $field,
        private readonly string $value,
        private readonly array $options = [],
    ) {
    }

    public function toArray(): array
    {
        $result = [
            'match' => [
                $this->field => [
                    'query' => $this->value,
                ],
            ],
        ];

        foreach ($this->options as $k => $v) {
            $result['match'][$this->field][$k] = $v;
        }

        return $result;
    }
}
