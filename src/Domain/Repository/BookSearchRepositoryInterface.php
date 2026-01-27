<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Domain\Repository;

use Dinargab\Homework14\Domain\Entity\Book;

interface BookSearchRepositoryInterface
{
    public function search(string $searchQuery, ?int $minPrice = null, ?int $maxPrice = null, ?string $category = null, bool $inStock = false): array;

    public function getBySku(string $sku): ?Book;

    public function bulkSave(array $books): void;

    public function clear(): void;
}