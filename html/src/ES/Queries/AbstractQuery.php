<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Queries;

abstract readonly class AbstractQuery
{
    /**
     * @return array
     */
    abstract public function toArray(): array;
}
