<?php

declare(strict_types=1);

namespace App\News\Domain\Repository;

use App\Shared\Domain\Repository\Pager;

class NewsFilter
{
    private array $newsIds = [];

    public function __construct(
        public ?Pager $pager = null,
        public ?string $search = null,
    ) {
    }

    public function getNewsIds(): array
    {
        return $this->newsIds;
    }

    public function setNewsIds(string ...$newsIds): void
    {
        $this->newsIds = $newsIds;
    }
}
