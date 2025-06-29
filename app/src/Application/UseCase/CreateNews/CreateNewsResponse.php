<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateNews;

use OpenApi\Attributes as OA;

readonly class CreateNewsResponse
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id
    )
    {
    }
}
