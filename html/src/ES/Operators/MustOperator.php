<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\ES\Operators;

use Otus\Elasticsearch\ES\Queries\AbstractQuery;

class MustOperator extends AbstractOperator
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'must' => array_filter(
                array_map(static function (AbstractQuery $query): array {
                    return $query->toArray();
                }, $this->queries)
            ),
        ];
    }
}
