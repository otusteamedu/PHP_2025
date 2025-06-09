<?php

declare(strict_types=1);

namespace App\Application\UseCase\ListNews;

readonly class ListNewsResponse
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public string $category,
        public string $annotation,
        public string $created_at,
    )
    {
    }
}
