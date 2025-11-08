<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShowNews;

use OpenApi\Attributes as OA;

readonly class ShowNewsResponse
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id,
        #[OA\Property(example: "News title")]
        public string $title,
        #[OA\Property(example: "John")]
        public string $author,
        #[OA\Property(example: "Sport")]
        public string $category,
        #[OA\Property(example: "News content")]
        public string $content,
        #[OA\Property(example: "2025-06-01 12:00:00")]
        public string $created_at,
    )
    {
    }
}
