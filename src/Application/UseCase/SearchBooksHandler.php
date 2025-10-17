<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\SearchRequest;
use App\Application\Port\BookSearchRepositoryInterface;

final readonly class SearchBooksHandler
{
    public function __construct(private BookSearchRepositoryInterface $repository)
    {
    }

    /**
     * @return array{items: array<int, array{title:string, sku:string, category:string, price:int, total_stock:int, score: float|null}>, total:int, max_score: float|null}
     */
    public function handle(SearchRequest $request): array
    {
        $result = $this->repository->search($request);
        $items = [];
        foreach ($result['items'] as $book) {
            $items[] = [
                'title' => $book->getTitle(),
                'sku' => $book->getSku(),
                'category' => $book->getCategory(),
                'price' => $book->getPrice(),
                'total_stock' => $book->getTotalStock(),
                'score' => $result['max_score'],
            ];
        }

        return [
            'items' => $items,
            'total' => $result['total'],
            'max_score' => $result['max_score'],
        ];
    }
}
