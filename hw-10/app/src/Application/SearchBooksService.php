<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\BookRepositoryInterface;

final readonly class SearchBooksService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
    ) {
    }

    public function search(?string $name, ?string $category, ?int $maxPrice, int $minPrice, bool $inStock): array
    {
        return $this->bookRepository->searchBooks($name, $category, $maxPrice, $minPrice, $inStock);
    }
}
