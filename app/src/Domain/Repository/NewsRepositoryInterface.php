<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\News;

interface NewsRepositoryInterface
{
    /**
     * @return News[]
     */
    public function findAll(): array;

    /**
     * @return News[]
     */
    public function findAllWithPagination(int $limit, int $offset): array;

    public function findOneById(int $id): ?News;

    public function save(News $news): void;
}
