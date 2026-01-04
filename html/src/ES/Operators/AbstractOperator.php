<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Operators;

use Otus\Elasticsearch\ES\Queries\AbstractQuery;

abstract class AbstractOperator
{
    /**
     * @param array $queries
     */
    public function __construct(protected array $queries = [])
    {
    }

    /**
     * @param AbstractQuery $query
     */
    public function push(AbstractQuery $query): void
    {
        $this->queries[] = $query;
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;
}
