<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Queries;

readonly class DummyQuery extends AbstractQuery
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
