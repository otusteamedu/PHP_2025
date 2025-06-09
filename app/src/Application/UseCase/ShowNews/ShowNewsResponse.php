<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShowNews;

readonly class ShowNewsResponse
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public string $category,
        public string $content,
        public string $created_at,
    )
    {
    }
}
