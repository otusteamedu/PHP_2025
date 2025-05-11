<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GetPaginatedNews;

use App\News\Domain\Repository\NewsFilter;

class GetPaginatedNewsRequest
{
    public function __construct(public NewsFilter $filter)
    {
    }
}
