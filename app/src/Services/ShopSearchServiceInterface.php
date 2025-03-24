<?php declare(strict_types=1);

namespace App\Services;

use App\Forms\Search\BookSearch;
use App\Models\Book;

/**
 * Interface ShopSearchServiceInterface
 * @package App\Services
 */
interface ShopSearchServiceInterface
{
    /**
     * @param BookSearch $bookSearch
     * @return Book[]
     */
    public function search(BookSearch $bookSearch): array;

    /**
     * @param BookSearch $bookSearch
     * @return int
     */
    public function count(BookSearch $bookSearch): int;
}