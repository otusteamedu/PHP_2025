<?php

namespace Arlex2305k\BooksShop\Service;

use Arlex2305k\BooksShop\Db\Elasticsearch;

class Search
{
    private Elasticsearch $repository;

    public function __construct(Elasticsearch $repository)
    {
        $this->repository = $repository;
    }

    public function search(string $query = '', array $options = []): array
    {
        $filters = [];

        if (!empty($options['category'])) {
            $filters['category'] = $options['category'];
        }

        if (isset($options['max_price'])) {
            $filters['max_price'] = (float)$options['max_price'];
        }

        if (isset($options['in_stock']) && $options['in_stock']) {
            $filters['in_stock'] = true;
        }

        return $this->repository->searchBooks($query, $filters);
    }
}
