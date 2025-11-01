<?php

namespace App\Search;

class SearchQueryBuilder
{
    private array $query;

    public function __construct(string $indexName)
    {
        $this->query = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => []
                    ]
                ]
            ]
        ];
    }

    public function withSearchTerm(string $term, array $fields = ['title', 'category']): self
    {
        $this->query['body']['query']['bool']['must'][] = [
            'multi_match' => [
                'query' => $term,
                'fields' => $fields,
                'fuzziness' => 'AUTO'
            ]
        ];

        return $this;
    }

    public function withCategory(string $category): self
    {
        $this->query['body']['query']['bool']['filter'][] = [
            'term' => ['category' => $category]
        ];

        return $this;
    }

    public function withMaxPrice(int $maxPrice): self
    {
        $this->query['body']['query']['bool']['filter'][] = [
            'range' => ['price' => ['lte' => $maxPrice]]
        ];

        return $this;
    }

    public function withMinStock(int $minStock): self
    {
        $this->query['body']['query']['bool']['filter'][] = [
            'nested' => [
                'path' => 'stock',
                'query' => [
                    'range' => ['stock.stock' => ['gte' => $minStock]]
                ]
            ]
        ];

        return $this;
    }

    public function build(): array
    {
        return $this->query;
    }
}