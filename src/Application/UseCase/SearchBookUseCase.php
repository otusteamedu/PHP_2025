<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\UseCase;

use Dinargab\Homework14\Application\DTO\BookDTO;
use Dinargab\Homework14\Application\DTO\SearchBookRequestDTO;
use Dinargab\Homework14\Application\DTO\SearchBookResponseDTO;
use Dinargab\Homework14\Domain\Entity\Book;
use Dinargab\Homework14\Domain\Repository\BookSearchRepositoryInterface;
use InvalidArgumentException;

class SearchBookUseCase
{
    public function __construct(
        private BookSearchRepositoryInterface $bookSearchRepository,
    ) {
    }

    public function __invoke(SearchBookRequestDTO $searchBookRequestDTO): SearchBookResponseDTO
    {
        if (empty($searchBookRequestDTO->searchQuery)) {
            throw new InvalidArgumentException('Search query is empty');
        }
        $books = $this->bookSearchRepository->search(
            $searchBookRequestDTO->searchQuery,
            $searchBookRequestDTO->minPrice,
            $searchBookRequestDTO->maxPrice,
            $searchBookRequestDTO->category,
            $searchBookRequestDTO->inStock
        );

        return new SearchBookResponseDTO(
            array_map(fn (Book $book) => BookDTO::fromBook($book), $books),
        );

    }
}