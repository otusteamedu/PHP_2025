<?php

namespace App\Application\Port;

use App\Domain\Entity\News;

interface NewsRepositoryInterface
{
    public function save(News $news): void;
    public function getList(): array;
}