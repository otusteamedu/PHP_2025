<?php

namespace App\Application\Port;

use App\Domain\Entity\News;

interface NewsRepository
{
    public function findById(int $id): ?News;
    public function save(News $news): void;
}