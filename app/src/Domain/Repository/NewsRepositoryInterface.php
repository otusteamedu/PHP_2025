<?php

namespace App\Domain\Repository;

use App\Domain\News;

interface NewsRepositoryInterface
{
    public function persist(News $entity): void;

    public function flush(): void;

    public function getLastInsertId(News $entity): int;

    public function findListNews(int $limit, int $offset): array;

    public function findByIds(array $ids): array;
}
