<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateNews;

use OpenApi\Attributes as OA;

readonly class CreateNewsRequest
{
    public function __construct(
        #[OA\Property(example: "News title")]
        public string $title,
        #[OA\Property(example: "John")]
        public string $author,
        #[OA\Property(example: "Sport")]
        public string $category,
        #[OA\Property(example: "News content")]
        public string $content,
    )
    {
    }
}
