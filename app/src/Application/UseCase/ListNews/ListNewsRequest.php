<?php

declare(strict_types=1);

namespace App\Application\UseCase\ListNews;

readonly class ListNewsRequest
{
    public function __construct(public int $page, public int $pageSize = 20)
    {
    }
}
