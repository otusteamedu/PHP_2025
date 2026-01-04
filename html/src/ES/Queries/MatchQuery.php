<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Queries;

readonly class MatchQuery extends AbstractQuery
{
    /**
     * @param string $field
     * @param string $value
     */
    public function __construct(protected string $field, protected string $value)
    {
    }

    /**
     * @return string[][]
     */
    public function toArray(): array
    {
        return ['match' => [$this->field => $this->value]];
    }
}
