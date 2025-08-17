<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch;

use App\Domain\Models\Book;
use App\Domain\Models\SearchCriteria;
use App\Domain\Repositories\BookRepositoryInterface;

final class ElasticsearchBookRepository implements BookRepositoryInterface
{
    /** Размер пакета для массовой индексации */
    private const int BATCH_SIZE = 1000;
    
    /** Максимальное количество результатов поиска */
    private const int SEARCH_SIZE = 100;

    public function __construct(
        private ElasticsearchClient $client,
        private IndexManager $indexManager
    ) {
    }

    /**
     * @inheritdoc
     */
    public function search(SearchCriteria $criteria): array
    {
        $query = $this->buildSearchQuery($criteria);
        
        $params = [
            'index' => $this->client->getIndexName(),
            'body' => [
                'query' => $query,
                'size' => self::SEARCH_SIZE,
                'sort' => [
                    '_score' => ['order' => 'desc']
                ]
            ]
        ];

        $response = $this->client->getClient()->search($params);
        $responseArray = $response->asArray();
        
        return $this->mapSearchResults($responseArray);
    }

    /**
     * @inheritdoc
     */
    public function bulkIndex(array $books): void
    {
        $batches = array_chunk($books, self::BATCH_SIZE);

        foreach ($batches as $batch) {
            $params = ['body' => []];

            foreach ($batch as $book) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $this->client->getIndexName(),
                        '_id' => $book->getId()
                    ]
                ];

                $params['body'][] = [
                    'title' => $book->getTitle(),
                    'category' => $book->getCategory(),
                    'price' => $book->getPrice(),
                    'stock' => $book->getStock(),
                    'total_stock' => $book->getTotalStock()
                ];
            }

            $this->client->getClient()->bulk($params);
        }

        $this->indexManager->refreshIndex();
    }

    /**
     * @inheritdoc
     */
    public function createIndex(): void
    {
        $this->indexManager->createIndex();
    }

    /**
     * @inheritdoc
     */
    public function indexExists(): bool
    {
        return $this->indexManager->indexExists();
    }

    /**
     * @inheritdoc
     */
    public function deleteIndex(): void
    {
        $this->indexManager->deleteIndex();
    }

    /**
     * Строит поисковый запрос для Elasticsearch
     */
    private function buildSearchQuery(SearchCriteria $criteria): array
    {
        $must = [];
        $filter = [];

        // Поиск по тексту с учетом морфологии и опечаток
        if ($criteria->hasQuery()) {
            $must[] = [
                'multi_match' => [
                    'query' => $criteria->getQuery(),
                    'fields' => ['title^3', 'category^2'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                    'operator' => 'or'
                ]
            ];
        }

        // Фильтр по категории
        if ($criteria->hasCategory()) {
            $filter[] = [
                'term' => [
                    'category.keyword' => $criteria->getCategory()
                ]
            ];
        }

        // Фильтр по максимальной цене
        if ($criteria->hasMaxPrice()) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'lte' => $criteria->getMaxPrice()
                    ]
                ]
            ];
        }

        // Фильтр по наличию в магазинах
        if ($criteria->isInStock()) {
            $filter[] = [
                'range' => [
                    'total_stock' => [
                        'gt' => 0
                    ]
                ]
            ];
        }

        $query = ['bool' => []];

        if (!empty($must)) {
            $query['bool']['must'] = $must;
        }

        if (!empty($filter)) {
            $query['bool']['filter'] = $filter;
        }

        if (empty($must) && empty($filter)) {
            $query = ['match_all' => new \stdClass()];
        }

        return $query;
    }

    /**
     * Преобразует результаты поиска в массив книг
     */
    private function mapSearchResults(array $response): array
    {
        $books = [];

        foreach ($response['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            $books[] = Book::fromArray([
                'id' => $hit['_id'],
                'title' => $source['title'],
                'category' => $source['category'],
                'price' => $source['price'],
                'stock' => $source['stock']
            ]);
        }

        return $books;
    }
}