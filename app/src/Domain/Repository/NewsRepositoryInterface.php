<?php

namespace App\Domain\Repository;

use App\Domain\News;

interface NewsRepositoryInterface
{
    public function persist(News $entity): void;

    public function flush(): void;
}
