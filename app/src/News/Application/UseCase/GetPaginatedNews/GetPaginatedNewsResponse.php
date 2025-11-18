<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GetPaginatedNews;

use App\Shared\Domain\Repository\Pager;

class GetPaginatedNewsResponse
{
    public function __construct(public array $news, public Pager $pager)
    {
    }
}
