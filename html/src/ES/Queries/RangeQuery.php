<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Queries;

readonly class RangeQuery extends AbstractQuery
{
    /**
     * @param string $field
     * @param string $operation
     * @param string $value
     */
    public function __construct(protected string $field, protected string $operation, protected string $value)
    {
    }

    /**
     * @return string[][]
     */
    public function toArray(): array
    {
        return ['range' => [$this->field => [$this->operation => $this->value]]];
    }
}
