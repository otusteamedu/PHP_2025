<?php

declare(strict_types=1);

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\News;
use App\Shared\Domain\Repository\PaginationResult;

interface NewsRepositoryInterface
{
    public function findById(string $id): ?News;

    public function add(News $news): void;

    public function findByFilter(NewsFilter $filter): PaginationResult;

}
