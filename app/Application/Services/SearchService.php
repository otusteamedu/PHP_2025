<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\SearchRequest;
use App\Application\DTOs\SearchResponse;
use App\Application\Validators\SearchCriteriaValidator;
use App\Domain\Models\SearchCriteria;
use App\Domain\Repositories\BookRepositoryInterface;

final readonly class SearchService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private SearchCriteriaValidator $validator
    ) {
    }

    /**
     * Выполняет поиск книг по запросу
     */
    public function search(SearchRequest $request): SearchResponse
    {
        $criteria = $this->createSearchCriteria($request);

        $this->validator->validate($criteria);

        $startTime = microtime(true);
        $books = $this->bookRepository->search($criteria);
        $took = (microtime(true) - $startTime) * 1000;

        return new SearchResponse(
            books: $books,
            total: count($books),
            took: $took
        );
    }

    /**
     * Создает критерии поиска из запроса
     */
    private function createSearchCriteria(SearchRequest $request): SearchCriteria
    {
        return new SearchCriteria(
            query: $request->getQuery(),
            category: $request->getCategory(),
            maxPrice: $request->getMaxPrice(),
            inStock: $request->isInStock()
        );
    }
}