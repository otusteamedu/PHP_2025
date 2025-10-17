<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Repository;

use Dinargab\Homework12\Data\Model\Book;

interface RepositoryInterface
{
    /**
     * @return Book[]
     */
    public function search(string $searchQuery, array $price, ?string $category = "", bool $inStock = false): array;

    public function getBySku(string $sku): Book;
}