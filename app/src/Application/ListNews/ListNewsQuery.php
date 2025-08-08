<?php

declare(strict_types=1);

namespace App\Application\ListNews;

class ListNewsQuery
{
    public function __construct(
        public int $page,
        public int $limit,
    )
    {
    }
}
