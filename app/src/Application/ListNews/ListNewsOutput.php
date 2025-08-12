<?php

declare(strict_types=1);

namespace App\Application\ListNews;

class ListNewsOutput
{
    public function __construct(
        /** @var ListNews $listNews */
        public array $listNews,
    )
    {
    }
}
