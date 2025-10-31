<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Application\DTO\SearchRequest;
use App\Domain\Book;

interface BookSearchRepositoryInterface
{
    /**
     * @return array{items: Book[], total: int, max_score: float|null}
     */
    public function search(SearchRequest $request): array;
}
