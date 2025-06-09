<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateNews;

readonly class CreateNewsRequest
{
    public function __construct(
        public string $title,
        public string $author,
        public string $category,
        public string $content,
    )
    {
    }
}
