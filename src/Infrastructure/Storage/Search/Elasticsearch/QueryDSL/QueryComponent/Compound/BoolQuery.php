<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\Compound;

use App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\Joining\NestedQuery;
use App\Infrastructure\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class BoolQuery implements QueryComponentInterface
{
    private array $must = [];
    private array $should = [];
    private array $filter = [];
    private array $mustNot = [];

    public function must(QueryComponentInterface $query): self
    {
        $this->must[] = $query->toArray();

        return $this;
    }

    public function should(QueryComponentInterface $query): self
    {
        $this->should[] = $query->toArray();

        return $this;
    }

    public function filter(QueryComponentInterface $query): self
    {
        $this->filter[] = $query->toArray();

        return $this;
    }

    public function mustNot(QueryComponentInterface $query): self
    {
        $this->mustNot[] = $query->toArray();

        return $this;
    }

    public function nested(BoolClause $boolClause, string $path, QueryComponentInterface $innerQuery): self
    {
        $nestedQuery = new NestedQuery($path, $innerQuery);

        return match ($boolClause) {
            BoolClause::Must => $this->must($nestedQuery),
            BoolClause::Filter => $this->filter($nestedQuery),
            BoolClause::Should => $this->should($nestedQuery),
            BoolClause::MustNot => $this->mustNot($nestedQuery),
        };
    }

    public function toArray(): array
    {
        $bool = array_filter([
            BoolClause::Must->value => $this->must,
            BoolClause::Should->value => $this->should,
            BoolClause::Filter->value => $this->filter,
            BoolClause::MustNot->value => $this->mustNot,
        ]);

        return !empty($bool) ? ['bool' => $bool] : [];
    }
}
