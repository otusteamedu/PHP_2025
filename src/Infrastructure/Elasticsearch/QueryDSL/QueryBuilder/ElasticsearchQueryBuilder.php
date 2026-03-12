<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\QueryDSL\QueryBuilder;

use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\QueryComponentInterface;

class ElasticsearchQueryBuilder
{
    public const string ASC = 'asc';
    public const string DESC = 'desc';

    private array $sort;

    public function __construct(
        private ?QueryComponentInterface $query = null,
        private int $size = 10,
        private int $from = 0,
        string $sortBy = '_score',
        string $sortOrder = self::DESC,
    ) {
        $this->setSort($sortBy, $sortOrder);
    }

    public function getQuery(): ?QueryComponentInterface
    {
        return $this->query;
    }

    public function setQuery(QueryComponentInterface $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(string $sortBy, string $sortOrder): self
    {
        $this->sort = [[$sortBy => ['order' => $sortOrder]]];

        return $this;
    }

    public function build(): array
    {
        if ($this->getQuery() === null) {
            throw new \RuntimeException('Query not specified');
        }

        return [
            'query' => $this->getQuery()->toArray(),
            'from' => $this->getFrom(),
            'size' => $this->getSize(),
            'sort' => $this->getSort(),
        ];
    }
}
