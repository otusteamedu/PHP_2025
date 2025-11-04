<?php declare(strict_types=1);

namespace App\Repositories;

use App\Forms\Search\BookSearch;
use App\Models\Book;

/**
 * Interface ShopSearchRepositoryInterface
 * @package App\Repositories
 */
interface ShopSearchRepositoryInterface
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
