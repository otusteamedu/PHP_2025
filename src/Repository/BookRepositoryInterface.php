<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Book;
use App\DTO\SearchParams;

/**
 * Интерфейс репозитория для работы с книгами
 */
interface BookRepositoryInterface
{
    /**
     * Поиск книг по параметрам
     *
     * @return Book[]
     */
    public function search(SearchParams $params): array;
}

